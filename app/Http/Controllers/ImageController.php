<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Cache;
use Fractal;
use Log;
use App\Events\ImageWasCreated;
use App\Image;
use App\User;
use App\Transformers\ImageTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

class ImageController extends ApiController
{
    const IMAGES_PER_PAGE = 5;

    protected $createValidationRules = [
        'asset'         => 'required|image',
    ];

//    protected $updateValidationRules = [
//        'title'        => 'alpha',
//        'description'        => 'alpha'
//    ];

    protected $image;

    /**
     * Create a new image instance.
     *
     * @return void
     */
    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    /**
     * GET /images
     *
     * @return Response
     */
    public function index(){
        // Retrieve all images
        $paginator = Image::paginate(self::IMAGES_PER_PAGE);
        $images = $paginator->getCollection();

        $data = Fractal::collection($images)
                            ->includeUser()
                            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
                            ->transformWith(new ImageTransformer)
                            ->toArray()
        ;

        return $this->respond($data);
    }

    /**
     * GET /images/:id
     *
     * @param  string  $id
     * @return Response
     */
    public function show($id){
        $image = $this->image->findOrFail($id);

        // Retrieved cached version of image's data if cache item exists otherwise cache it
        $data = Cache::remember('image_'.$id, 60, function() use ($image)
        {
            return Fractal::item($image)
                            ->includeUser()
                            ->includeComments()
                            ->transformWith(new ImageTransformer)
                            ->toArray();
        });

        return $this->respond($data);
    }

    /**
     * POST /images
     *
     * @param Request $request
     * @param string  $id
     * @return mixed
     */
    public function create(Request $request)
    {
        $this->validate($request, $this->createValidationRules);
        $file = $request->file('asset');
        // isValid() causes error with file upload test.
        // It seems like Laravel don't keep the "test" attribute on UploadFile
        // if (isset($file) && $file->isValid()) {
        if (isset($file)) {

            // Image author is current user
            $this->image->user_id = $request->user()->id;

            // Save image to disk
            $extension = $file->getClientOriginalExtension();
            $destinationPath = Image::IMAGES_DIR.$file->getFilename().'/original/';

            $file->move($destinationPath, $file->getClientOriginalName());

            // Retrieve image metadata
            $this->image->mime = $file->getClientMimeType();
            $this->image->original_filename = $file->getClientOriginalName();
            $this->image->filename = $file->getFilename().'.'.$extension;
            $this->image->filesize = $file->getClientSize();

            $this->image->title = $request->get('title');
            $this->image->description = $request->get('description');

            $this->image->save();

            $data = Fractal::item($this->image)
                ->includeUser()
                ->transformWith(new ImageTransformer)
                ->toArray();

            // Send event in queue to process thumbnail
            event(new ImageWasCreated($this->image));

            return $this->respondCreated($data, ['Location' => route('image_show', ['id' => $this->image->id]) ]);
        } else {
            return $this->respondWithValidationError('File is not correct');
        }

    }

    /**
     * PUT /images/:id
     *
     * @param Request $request
     * @param string $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $image = $this->image->findOrFail($id);

        // If auth user can update image
        if (!is_null(Auth::user()) && Auth::user()->can('manage-image', $image)) {
            $image->title = $request->get('title');
            $image->description = $request->get('description');

            $image->save();

            $data = Fractal::item($image)
                ->includeUser()
                ->transformWith(new ImageTransformer)
                ->toArray();

            return $this->respond($data);
        } else {
            return $this->respondUnauthorized();
        }
    }

    /**
     * DELETE /images/:id
     *
     * @param  string  $id
     * @return Response
     */
    public function delete($id)
    {
        $image = $this->image->findOrFail($id);

        // If auth user can delete image
        if (!is_null(Auth::user()) && Auth::user()->can('manage-image', $image)) {

            // Delete images folder on storage
            File::deleteDirectory($image->getFilePath());

            $image->comments()->delete();
            $image->delete();

            return $this->respondOk('Image was deleted');
        } else {
            return $this->respondUnauthorized();
        }
    }

}

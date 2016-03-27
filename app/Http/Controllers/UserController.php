<?php

namespace App\Http\Controllers;

use Auth;
use Cache;
use Fractal;
use App\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class UserController extends ApiController
{

    protected $user;

    protected $createValidationRules = [
        'firstname'    => 'required',
        'lastname'     => 'required',
        'email'        => 'required|unique:users|email',
        'password'     => 'required|confirmed|min:8',
    ];

    protected $updateValidationRules = [
        'email'        => 'unique:users|email',
        'password'     => 'confirmed|min:8',
    ];


    /**
     * Create a new user instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * GET /users/:id
     *
     * @param  string  $id
     * @return Response
     */
    public function show($id){
        $user  = $this->user->findOrFail($id);

        $data = Cache::remember('user_'.$id, 60, function() use ($user) {
            return $data = Fractal::item($user)
                                    ->includeImages()
                                    ->transformWith(new UserTransformer)
                                    ->toArray();
        });

        return $this->respond($data);
    }

    /**
     * POST /users
     *
     * @param Request $request
     * @return mixed
     */
    public function create(Request $request)
    {
        $this->validate($request, $this->createValidationRules);

        $this->user->firstname = $request->get('firstname');
        $this->user->lastname = $request->get('lastname');
        $this->user->password = Crypt::encrypt($request->get('password'));
        $this->user->email = $request->get('email');

        $this->user->save();

        $data = Fractal::item($this->user)
            ->includeImages()
            ->transformWith(new UserTransformer)
            ->toArray();

        return $this->respondCreated($data, ['Location' => route('user_show', ['id' => $this->user->id]) ]);
    }

    /**
     * PUT /users/:id
     *
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        if(!is_null(Auth::user()) && Auth::user()->id == $id){
            $this->validate($request, $this->updateValidationRules);

            $user = $this->user->findOrFail($id);

            if ($request->has('firstname')) {
                $user->firstname = $request->get('firstname');
            }

            if ($request->has('lastname')) {
                $user->lastname = $request->get('lastname');
            }

            $user->save();

            $data = Fractal::item($user)
                ->includeImages()
                ->transformWith(new UserTransformer)
                ->toArray();

            return $this->respond($data);
        } else {
            return $this->respondUnauthorized();
        }
    }

}

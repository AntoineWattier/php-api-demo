<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Response as IlluminateResponse;
use Laravel\Lumen\Routing\Controller as BaseController;

class ApiController extends BaseController {
    
    /**
     * @var int
     */
    protected $statusCode = IlluminateResponse::HTTP_OK;

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     * @return mixed
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function respondNotFound($message = 'Not Found')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_NOT_FOUND)->respondWithError($message);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function respondUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_UNAUTHORIZED)->respondWithError($message);
    }

    /**
     * @param string $message
     * @return mixed
     */

    public function respondInternalError($message = 'Internal Server Error')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR)->respondWithError($message);
    }

    /**
     * @param $data
     * @param $headers
     * @return mixed
     */
    public function respondCreated($data, $headers = [])
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_CREATED)->respond($data, $headers);
    }

    /**
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondOk($message)
    {
        return $this->respond([
            'message' => $message
        ]);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function respondWithValidationError($message = 'Unprocessable Entity')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY)->respondWithError($message);
    }

    /**
     * @param $data
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }

    /**
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondWithError($message)
    {
        return $this->respond([
            'error' => [
                'message'     => $message,
                'status_code' => $this->getStatusCode()
            ]
        ]);
    }

    public function respondWithCORS($data)
    {
        return $this->respond($data, $this->setCORSHeaders());
    }
    
    private function setCORSHeaders()
    {
        $header['Access-Control-Allow-Origin'] = '*';
        $header['Allow'] = 'GET, POST, OPTIONS';
        $header['Access-Control-Allow-Headers'] = 'Origin, Content-Type, Accept, Authorization, X-Request-With';
        $header['Access-Control-Allow-Credentials'] = 'true';
        return $header;
    }
}
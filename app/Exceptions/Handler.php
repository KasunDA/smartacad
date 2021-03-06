<?php

namespace App\Exceptions;

use BadMethodCallException;
use Exception;
use ErrorException;
use Illuminate\Database\QueryException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use PDOException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
//        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json($exception->errors(), $exception->status);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        ////////////////////////////////////////////////// starts: KHEENGZ CUSTOM CODE ///////////////////////////////////////
        //Invalid Record Request Exception
        if ($e instanceof ModelNotFoundException) {
            return response()->view('errors.custom', [
                'code'=>'304.1',
                'header'=>'Invalid Record Request',
                'message'=>'The Record You Are Looking For Does Not Exist <a href="/">Return Back home </a><br/>'
            ]);
        }
        //File For Download Not Found Exception
        if ($e instanceof FileNotFoundException){
            return response()->view('errors.custom', [
                'code'=>'501.4',
                'header'=>'File Not Found',
                'message'=>'The File You Are Looking For Or Trying To Download Does Not Exist On Our Server <a href="/"> Return Back home </a><br/>'
            ]);
        }
        //File For Download Not Found Exception
        if ($e instanceof FatalErrorException){
            return response()->view('errors.custom', [
                'code'=>'503',
                'header'=>'Fatal Error',
                'message'=>'<strong>Whoops!!!</strong> Something went wrong kindly retry again<br>' . $e->getMessage() . ' <a href="/">Return Back home </a><br/>'
            ]);
        }

        //File For Download Not Found Exception
        if ($e instanceof InvalidArgumentException){
            return response()->view('errors.custom', [
                'code'=>'207',
                'header'=>'Unexpected data found',
                'message'=>'<strong>Whoops!!!</strong> Something went wrong kindly retry again.<br>' . $e->getMessage() . ' <a href="/">Return Back home </a><br/>'
            ]);
        }
        //Query Exception
        if ($e instanceof QueryException){
            return response()->view('errors.custom', [
                'code'=>'207',
                'header'=>'Query Exception',
                'message'=>'<strong>Whoops!!!</strong> Something went wrong kindly retry again<br>' . $e->getMessage() . ' <a href="/">Return Back home </a><br/>'
            ]);
        }
        //Logical Error Exception
        if ($e instanceof ErrorException){
            return response()->view('errors.custom', [
                'code'=>'504.3',
                'header'=>'Critical Error',
                'message'=>'<strong>Whoops!!!</strong> Something went wrong kindly retry again<br>' . $e->getMessage() . ' <a href="/">Return Back home </a><br/>'
            ]);
        }
        // Bad Network Issues Method NotAllowed HttpException
        if ($e instanceof MethodNotAllowedHttpException){
            return response()->view('errors.custom', [
                'code'=>'507.3',
                'header'=>'Method Not Allowed',
                'message'=>'<strong>Whoops!!!</strong> Something went wrong with your network kindly retry again and 
                    <strong>allow the page to load completely</strong><br>' . $e->getMessage() . ' <a href="/">Return Back home </a><br/>'
            ]);
        }
        // Bad Method Call Exception
        if ($e instanceof BadMethodCallException){
            return response()->view('errors.custom', [
                'code'=>'502.3',
                'header'=>'Bad Method Call',
                'message'=>'<strong>Whoops!!!</strong> Something went wrong with your network kindly retry again and 
                    <strong>allow the page to load completely</strong><br>' . $e->getMessage() . ' <a href="/">Return Back home </a><br/>'
            ]);
        }
        
        // Bad Network Issues Method NotAllowed HttpException
        if ($e instanceof PDOException){
            return response()->view('errors.custom', [
                'code'=>'509',
                'header'=>'Error establishing a database connection',
                'message'=>'<strong>Whoops!!!</strong> Something went wrong with your our server kindly retry few minutes later or 
                    <strong> Contact your systems administrator</strong><br>' . $e->getMessage() . ' <a href="/">Return Back home </a><br/>'
            ]);
        }
        //If Token Mismatch Exception Occur i.e csrf error
        if ($e instanceof TokenMismatchException){
            Auth::logout();
            return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
        }
        ////////////////////////////////////////////////// end: KHEENGZ CUSTOM CODE ///////////////////////////////////////

        return parent::render($request, $e);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }
}

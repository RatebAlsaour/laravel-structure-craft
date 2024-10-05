<?php


namespace rateb\structure\Services;

use Illuminate\Support\Facades\App;

class ApiResponseService
{
    public static function successResponse($data, $code = 200, $lang = 'ar')
    {
        $lang=App::getLocale();
        return response()->json(
            [
                'message' => trans('messages.successResponse'),
                'success'   => true,
                'data'      => $data
            ],
            $code
        );
    }

    public static function validateResponse($errors, $lang = 'ar')
    {
        $lang=App::getLocale();

        return response()->json(
            [
                'message' => ($lang == 'en') ? 'An error occurred' : 'حدث خطأ',
                'success'   => false,
                'errors'    => $errors->all()
            ],
            422
        );
    }

    public static function deletedResponse($msg = null, $code = 200, $lang = 'ar')
    {
        $lang=App::getLocale();

        if (is_null($msg)) $msg = trans('messages.deletedResponse');

        return response()->json(
            [
                'message'   => $msg,
                'success'   => true,
                'data'      => []
            ],
            $code
        );
    }

    public static function createdResponse($msg = null, $code = 200, $lang = 'ar')
    {
        $lang=App::getLocale();


        if (is_null($msg)) $msg = trans('messages.createdResponse');

        return response()->json(
            [
                'message'   => $msg,
                'success'   => true,
                'data'      => []
            ],
            $code
        );
    }

    public static function successMsgResponse($msg = null, $lang = 'ar')
    {
        $lang=App::getLocale();



        if (is_null($msg)) $msg = trans('messages.successResponse');

        return response()->json(
            [
                'message'   => $msg,
                'success'   => true,
                'data'      => []
            ],
            200
        );
    }

    public static function notFoundResponse($msg = null, $lang = 'ar')
    {
        $lang=App::getLocale();


        if (is_null($msg)) $msg = ($lang == 'en') ? 'not_found' : 'غير موجود';

        return response()->json(
            [
                'message'   => $msg,
                'success'   => false,
                'errors'    => [$msg]
            ],
            404
        );
    }

    public static function unauthorizedResponse($msg = null, $code = 401)
    {
        $lang=App::getLocale();

        return response()->json(
            [
                'success'   => false,
                'errors'    => [$msg ?? trans('unauthorized')]
            ],
            $code
        );
    }

    public static function activeResponse($msg = null, $code = 401)
    {
        return response()->json(
            [
                'success'   => false,
                'errors'    => [$msg ?? trans('unauthorized')]
            ],
            $code
        );
    }

    public static function unauthenticatedResponse($msg = null, $code = 401)
    {
        return response()->json(
            [
                'success'   => false,
                'errors'    => [$msg ?? trans('response.unauthenticated')]
            ],
            $code
        );
    }

    public static function errorMsgResponse($msg = null, $lang = 'ar')
    {

        if (is_null($msg)) $msg = trans('messages.errorMsgResponse');

        return response()->json(
            [
                'message'   => $msg,
                'success'   => false,
                'errors'    => [$msg]
            ],
            400
        );
    }


    public static function loginResponse($user, $token, $refreshToken)
    {



        return response()->json(
            [
                'message' => trans('messages.login'),
                'user' => $user,
                'verified' => $user->hasVerifiedEmail(),
                'token' => [
                    'access_token' => $token,
                    'refersh_token' => $refreshToken,
                    'type' => 'bearer',
                ]
            ],
            200
        );
    }
    //this arrangement this existing order
    public static function errorWithDataResponse($data, $code = 422)
    {
        $dddd = json_encode($data);
        // استخراج جميع الأحرف باستثناء الأرقام
        $nonNumericCharacters = preg_replace('/["]/', '', $dddd);

        $lang = App::getLocale();
        $msg = ($lang == 'en') ? 'This arrangement this existing order' : ' هذا الترتيب الموجود';

        // التحقق إذا كانت $data مصفوفة وتحويلها إلى نص إذا كانت
        if (is_array($data)) {
            $data = implode(', ', $data);
        }

        return response()->json([
            'message' => $msg . " " . $nonNumericCharacters,
            'success' => false,
            'data' => $data,
        ], $code);
    }
}

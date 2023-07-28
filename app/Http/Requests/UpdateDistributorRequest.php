<?php

namespace App\Http\Requests;

use App\User;
use App\Distributor;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateDistributorRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('distributor_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules(Request $request)
    {
        $id = $request->segment(3);
        $distributor = Distributor::find($id);
        return [
            'name'    => [
                'required',
                'unique:users,name,'.$distributor->distributor_id.',id,deleted_at,NULL',
            ],
            'username'   => [
                'required',
                'unique:users,username,'.$distributor->distributor_id.',id,deleted_at,NULL',
            ],
            'roles.*' => [
                'integer',
            ],
            'roles'   => [
                'required',
                'array',
            ],
            'terminals.*' => [
                'integer',
            ],
            'terminals'   => [
                'array',
            ],
            //'ships.*' => [
                //'integer',
            //],
            //'ships'   => [
                //'array',
            //],

        ];
    }
}

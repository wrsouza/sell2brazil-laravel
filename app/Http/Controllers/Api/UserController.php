<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::paginate();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $data = $this->filterData($request->all());

        $validator = $this->validator($data, $this->getRules());
        if ($validator->fails()) {
            return response()->json($validator->errors())->setStatusCode(400);
        }

        return User::create($data);
    }

    private function validator(array $data, array $rules)
    {
        return Validator::make($data, $rules, $this->getMessages());
    }

    private function getRules(User $user = null)
    {
        if (!$user) {
            return [
                'name' => 'required|max:255|min:5',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6|max:20|confirmed',
            ];
        }

        return [
            'name' => 'max:255|min:5',
            'email' => ['email', Rule::unique('users')->ignore($user)],
            'password' => 'min:6|max:20|confirmed',
        ];
    }

    private function getMessages()
    {
        return [
            'required' => 'O campo é obrigatório!',
            'email' => 'E-mail inválido!',
            'max' => 'Máximo de :max caracteres!',
            'min' => 'Mínimo de :min caracteres!',
            'unique' => ':input já em uso!',
            'confirmed' => 'A Confirmação de Senha não confere!'
        ];
    }

    private function filterData($values)
    {
        $data = [];
        if (empty($values) || !is_array($values)) {
            return $data;
        }
        foreach ($values as $key => $value) {
            if (is_string($value) && trim(strip_tags($value))) {
                $data[$key] = trim(strip_tags($value));
            }
            if (is_array($value) && !empty($value)) {
                $data[$key] = $this->filterData($value);
            }
            if (is_int($value) || is_float($value)) {
                $data[$key] = $value;
            }
        }
        return $data;
    }
}

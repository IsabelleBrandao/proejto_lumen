<?php

namespace App\Http\Controllers;
use App\Models\Usuario; 
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{

    protected $jwt;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
        $this->middleware('auth:api', [
            'except' => ['usuarioLogin', 'cadastrarUsuario']
        ]);
    }

    public function usuarioLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        if(!$token = $this->jwt->attempt($request->only('email', 'password' )))
        { 
            return response()->json(['Usuario não encontrado'], 404);
        }

        return response()->json(compact('token'));
    }

    public function mostrarTodosUsuarios()
    {
        return response()->json(Usuario::all()); //retorna usuarios do banco
    }

    public function cadastrarUsuario(Request $request)
    {
        //validação
        $this->validate($request, [
            'usuario' => 'required|min:5|max:40',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required'
        ]);
        //inserindo um registro 
        $usuario = new Usuario;
        $usuario->email = $request->email;
        $usuario->usuario = $request->usuario;
        $usuario->password = Hash::make($request->password);

        //salvando registro no banco
        $usuario->save();
        return response()->json($usuario);
    }

    public function mostrarUsuarioAutenticado()
    {
        $usuario = Auth::user();

        return response()->json($usuario);
    }    

    public function mostrarUmUsuario($id)
    {
        return response()->json(Usuario::find($id));
    }

    public function atualizarUsuario($id, Request $request)
    {
        $usuario = Usuario::find($id);

        $usuario->email = $request->email;
        $usuario->usuario = $request->usuario;
        $usuario->password = $request->password;

        $usuario->save();
        return response()->json($usuario);
    }

    public function deletarUsuario($id)
    {
        $usuario = Usuario::find($id);
        $usuario->delete();
        return response()->json('Deletado com Sucesso', 200);

    }

    public function usuarioLogout()
    {
        Auth::logout(); //função que invalida o token 
        return response()->json("Usuario deslogou com sucesso!");
    }
}

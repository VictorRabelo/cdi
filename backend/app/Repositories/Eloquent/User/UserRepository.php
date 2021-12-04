<?php

namespace App\Repositories\Eloquent\User;

use App\Models\Role;
use App\Models\User;
use App\Repositories\Contracts\User\UserRepositoryInterface;
use App\Repositories\Eloquent\AbstractRepository;
use App\Utils\Messages;
use Illuminate\Support\Facades\Hash;

class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
    /**
     * @var User
    */
    protected $model = User::class;

    /**
     * @var Messages
     */
    protected $messages = Messages::class;

    public function index()
    {
        return $this->model->orderBy('name', 'asc')->get();
    }
    
    public function create($dados)
    {
        $dados['password'] = Hash::make($dados['password']);
        
        $dados = $this->store($dados);
        
        if ($dados) {
            $role = new Role(['role' => 'user']);
            $dados->role()->save($role);
            
            return $this->messages->update;
        }

        return false;
    }
}
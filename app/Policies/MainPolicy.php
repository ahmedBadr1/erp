<?php

namespace App\Policies;

use App\Models\User;
use Exception;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;

class MainPolicy
{
    use HandlesAuthorization;

    /**
     * @var string
     */
    protected string $userIdColumn = 'user_id';


    /**
     * @var array|string[]
     */
    protected array $morphColumns = [
        'model_type',
        'model_id'
    ];


    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Model $model
     * @return Response|bool
     * @throws Exception
     */
    public function view(User $user, Model $model): Response|bool
    {
        return $this->authorize($user, $model, __FUNCTION__);
    }


    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Model $model
     * @return Response|bool
     * @throws Exception
     */
    public function update(User $user, Model $model): Response|bool
    {
        return $this->authorize($user, $model, __FUNCTION__);
    }


    /**
     * @param User $user
     * @param Model $model
     * @param string|null $ability
     * @return Response
     * @throws Exception
     */
    private function authorize(User $user, Model $model, string $ability = null): Response
    {
        switch (true) {
            case $this->isAdmin($user):
                $condition = $this->adminHasPermission($user, $model, $ability);
                break;
            case (Schema::hasColumn($model->getTable(), $this->userIdColumn)):
                $condition = $this->relatesTo($user, $model);
                break;
            case (Schema::hasColumns($model->getTable(), $this->morphColumns)):
                $condition = $this->morphsTo($user, $model);
                break;
            default:
                throw new Exception('User not authorized.');
        }
        return $condition ? $this->allow() : $this->deny();
    }


    /**
     * @param User|AdminUser $user
     * @return bool
     */
    protected function isAdmin(AdminUser|User $user): bool
    {
        return $user instanceof AdminUser;
    }


    /**
     * @param AdminUser $user
     * @param Model|string $model
     * @param string $ability
     * @return bool
     * @throws ReflectionException
     */
    protected function adminHasPermission(AdminUser $user, Model|string $model, string $ability): bool
    {
        $class = is_string($model) ? $model : get_class($model);
        $permission = $this->computePermission($ability, $class);


        return $this->permissionIsRegisteredInConfig($class, $permission) && $user->hasPermission($permission);
    }


    /**
     * @param string $ability
     * @param string $class
     * @return string
     * @throws ReflectionException
     */
    private function computePermission(string $ability, string $class): string
    {
        $reflectionClass = new ReflectionClass($class);
        $kebabAbility = Str::kebab($ability);
        $kebabClassName = Str::kebab($reflectionClass->getShortName());
        return "{$kebabAbility}-{$kebabClassName}";
    }


    /**
     * @param string $class
     * @param string $permission
     * @return bool
     */
    private function permissionIsRegisteredInConfig(string $class, string $permission): bool
    {
        $value = config("model-permissions.{$class}.values.{$permission}");
        return !empty($value);
    }


    /**
     * @param User $user
     * @param Model $model
     * @return bool
     */
    private function relatesTo(User $user, Model $model): bool
    {
        $relatedUserId = $model->{$this->userIdColumn};


        return is_int($relatedUserId) && $user->id === $relatedUserId;
    }


    /**
     * @throws Exception
     */
    private function morphsTo(User $user, Model $model): bool
    {
        if ($model->model_type === User::class) {
            return $user->id === $model->model_id;
        }


        $morphedModel = $model->model;


        if ($morphedModel instanceof Model) {
            return $this->authorize($user, $morphedModel)->allowed();
        }


        throw new Exception('Expected instance of Model but something else was given!');
    }
}

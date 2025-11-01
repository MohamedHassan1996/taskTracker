<?php

namespace App\Services\User;

use App\Enums\User\UserStatus;
use App\Filters\User\FilterUser;
use App\Filters\User\FilterUserRole;
use App\Models\User;
use App\Services\Upload\UploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserService{


    public function __construct(protected UploadService $uploadService)
    {
    }

    public function allUsers()
    {
        $user = QueryBuilder::for(User::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new FilterUser()), // Add a custom search filter
                AllowedFilter::exact('status'),
                AllowedFilter::custom('role', new FilterUserRole()),
            ])->paginate(request()->get('pageSize', 10));

        return $user;

    }

    public function createUser(array $userData): User
    {

        $avatarPath = null;

        if(isset($userData['avatar']) && $userData['avatar'] instanceof UploadedFile){
            $avatarPath =  $this->uploadService->uploadFile($userData['avatar'], 'avatars');
        }

        $user = User::create([
            'first_name' => $userData['firstName'],
            'last_name' => $userData['lastName'],
            'username' => $userData['username'],
            'email' => $userData['email'],
            'phone' => $userData['phone'],
            'address' => $userData['address'],
            'password' => $userData['password'],
            'status' => UserStatus::from($userData['status'])->value,
            'avatar' => $avatarPath,
            'per_hour_rate' => $userData['perHourRate']??0
        ]);

        $role = Role::find($userData['roleId']);

        $user->assignRole($role->id);

        return $user;

    }

    public function editUser(int $userId)
    {
        return User::where('id', $userId)->with('roles')->first();
    }

    public function updateUser(array $userData): User
    {

        $avatarPath = null;

        if(isset($userData['avatar']) && $userData['avatar'] instanceof UploadedFile){
            $avatarPath =  $this->uploadService->uploadFile($userData['avatar'], $userData['avatar']??'avatars');
        }

        $user = User::find($userData['userId']);
        $user->first_name = $userData['firstName'];
        $user->last_name = $userData['lastName'];
        $user->username = $userData['username'];
        $user->email = $userData['email'];
        $user->phone = $userData['phone'];
        $user->address = $userData['address'];
        $user->per_hour_rate = $userData['perHourRate']??0;

        if(isset($userData['password'])){
            $user->password = $userData['password'];
        }

        $user->status = UserStatus::from($userData['status'])->value;

        if($avatarPath){
            Storage::disk('public')->delete($user->avatar);
            $user->avatar = $avatarPath;
        }

        $user->save();

        $role = Role::find($userData['roleId']);

        $user->syncRoles($role->id);

        return $user;

    }


    public function deleteUser(int $userId)
    {

        $user = User::find($userId);
        if($user->avatar){
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

    }

    public function changeUserStatus(int $userId, int $status)
    {

        return User::where('id', $userId)->update(['status' => UserStatus::from($status)->value]);

    }


}

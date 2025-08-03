<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Obter dados do usuário autenticado
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'profile_image_url' => $user->profile_image ? Storage::url($user->profile_image) : null,
            ]
        ]);
    }

    /**
     * Atualizar perfil do usuário
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'sometimes|nullable|string|max:20',
            'address' => 'sometimes|nullable|string|max:255',
            'date_of_birth' => 'sometimes|nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($validator->validated());

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user->fresh(),
                'profile_image_url' => $user->profile_image ? Storage::url($user->profile_image) : null,
            ],
            'message' => 'Profile updated successfully'
        ]);
    }

    /**
     * Atualizar senha do usuário
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password does not match'
            ], 401);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully'
        ]);
    }

    /**
     * Atualizar imagem de perfil
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfileImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Remove a imagem antiga se existir
        if ($user->profile_image) {
            Storage::delete($user->profile_image);
        }

        // Armazena a nova imagem
        $path = $request->file('profile_image')->store('profile_images');

        $user->update([
            'profile_image' => $path
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'profile_image_url' => Storage::url($path)
            ],
            'message' => 'Profile image updated successfully'
        ]);
    }

    /**
     * Remover imagem de perfil
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteProfileImage(Request $request)
    {
        $user = $request->user();

        if (!$user->profile_image) {
            return response()->json([
                'success' => false,
                'message' => 'No profile image to delete'
            ], 404);
        }

        Storage::delete($user->profile_image);

        $user->update([
            'profile_image' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile image deleted successfully'
        ]);
    }
}

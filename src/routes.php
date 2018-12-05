<?php

Route::group(['middleware' => 'web'], function () {

    // auth
    Route::get('admin/login', config('lap.controllers.auth.login') . '@loginForm')->name('admin.login');
    Route::post('admin/login', config('lap.controllers.auth.login') . '@login');
    Route::post('admin/logout', config('lap.controllers.auth.login') . '@logout')->name('admin.logout');
    Route::get('admin/profile', config('lap.controllers.auth.profile') . '@updateForm')->name('admin.profile');
    Route::patch('admin/profile', config('lap.controllers.auth.profile') . '@update');
    Route::get('admin/password/change', config('lap.controllers.auth.change_password') . '@changeForm')->name('admin.password.change');
    Route::patch('admin/password/change', config('lap.controllers.auth.change_password') . '@change');
    Route::get('admin/password/reset', config('lap.controllers.auth.forgot_password') . '@emailForm')->name('admin.password.request');
    Route::post('admin/password/email', config('lap.controllers.auth.forgot_password') . '@sendResetLinkEmail')->name('admin.password.email');
    Route::get('admin/password/reset/{token?}', config('lap.controllers.auth.reset_password') . '@resetForm')->name('admin.password.reset');
    Route::post('admin/password/reset', config('lap.controllers.auth.reset_password') . '@reset')->name('admin.password.update');

    // backend
    Route::get('admin', config('lap.controllers.backend') . '@index')->name('admin');
    Route::get('admin/dashboard', config('lap.controllers.backend') . '@dashboard')->name('admin.dashboard');
    Route::get('admin/settings', config('lap.controllers.backend') . '@settingsForm')->name('admin.settings');
    Route::patch('admin/settings', config('lap.controllers.backend') . '@settings');

    // role
    Route::get('admin/roles', config('lap.controllers.role') . '@index')->name('admin.roles');
    Route::get('admin/roles/create', config('lap.controllers.role') . '@createForm')->name('admin.roles.create');
    Route::post('admin/roles/create', config('lap.controllers.role') . '@create');
    Route::get('admin/roles/read/{id}', config('lap.controllers.role') . '@read')->name('admin.roles.read');
    Route::get('admin/roles/update/{id}', config('lap.controllers.role') . '@updateForm')->name('admin.roles.update');
    Route::patch('admin/roles/update/{id}', config('lap.controllers.role') . '@update');
    Route::delete('admin/roles/delete/{id}', config('lap.controllers.role') . '@delete')->name('admin.roles.delete');

    // user
    Route::get('admin/users', config('lap.controllers.user') . '@index')->name('admin.users');
    Route::get('admin/users/create', config('lap.controllers.user') . '@createForm')->name('admin.users.create');
    Route::post('admin/users/create', config('lap.controllers.user') . '@create');
    Route::get('admin/users/read/{id}', config('lap.controllers.user') . '@read')->name('admin.users.read');
    Route::get('admin/users/update/{id}', config('lap.controllers.user') . '@updateForm')->name('admin.users.update');
    Route::patch('admin/users/update/{id}', config('lap.controllers.user') . '@update');
    Route::get('admin/users/password/{id}', config('lap.controllers.user') . '@passwordForm')->name('admin.users.password');
    Route::patch('admin/users/password/{id}', config('lap.controllers.user') . '@password');
    Route::delete('admin/users/delete/{id}', config('lap.controllers.user') . '@delete')->name('admin.users.delete');

    // activity_logs
    Route::get('admin/activity_logs', config('lap.controllers.activity_log') . '@index')->name('admin.activity_logs');
    Route::get('admin/activity_logs/read/{id}', config('lap.controllers.activity_log') . '@read')->name('admin.activity_logs.read');
    
    // docs
    Route::get('docs/{id?}/{slug?}', config('lap.controllers.doc') . '@frontend')->name('docs');
    Route::get('admin/docs', config('lap.controllers.doc') . '@index')->name('admin.docs');
    Route::get('admin/docs/create', config('lap.controllers.doc') . '@createForm')->name('admin.docs.create');
    Route::post('admin/docs/create', config('lap.controllers.doc') . '@create');
    Route::get('admin/docs/read/{id}', config('lap.controllers.doc') . '@read')->name('admin.docs.read');
    Route::get('admin/docs/update/{id}', config('lap.controllers.doc') . '@updateForm')->name('admin.docs.update');
    Route::patch('admin/docs/update/{id}', config('lap.controllers.doc') . '@update');
    Route::patch('admin/docs/move/{id}', config('lap.controllers.doc') . '@move')->name('admin.docs.move');
    Route::delete('admin/docs/delete/{id}', config('lap.controllers.doc') . '@delete')->name('admin.docs.delete');
    
});
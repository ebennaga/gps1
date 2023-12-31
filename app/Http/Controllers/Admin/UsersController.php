<?php

namespace App\Http\Controllers\Admin;

use App\EmailUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Role;
use App\Terminal;
use App\User;
use App\Distributor;
use App\Ship;
use Gate;
use Illuminate\Http\Request;
use PharIo\Manifest\Email;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Auth;
use Session;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            //get data by role as
            $query = $this->queryIndexAjax();

            $table = Datatables::of($query);
            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn(
                'actions',
                function ($row) {
                    $viewGate      = 'user_show';
                    $editGate      = 'user_edit';
                    $deleteGate    = 'user_delete';
                    $crudRoutePart = 'users';
                    return view(
                        'partials.datatablesActions',
                        compact(
                            'viewGate',
                            'editGate',
                            'deleteGate',
                            'crudRoutePart',
                            'row'
                        )
                    );
                }
            );
            $table->editColumn(
                'id',
                function ($row) {
                    return $row->id ? $row->id : "";
                }
            );
            $table->editColumn(
                'name',
                function ($row) {
                    return $row->name ? $row->name : "";
                }
            );
            $table->editColumn(
                'username',
                function ($row) {
                    return $row->username ? $row->username : "";
                }
            );
            $table->editColumn(
                'roles',
                function ($row) {
                    $labels = [];
                    foreach ($row->roles as $role) {
                        $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $role->title);
                    }
                    return implode(', ', $labels);
                }
            );
            $table->editColumn(
                'email',
                function ($row) {
                    $labels = [];
                    foreach ($row->email as $email) {
                        $labels[] = $email->email;
                    }
                    return implode(', ', $labels);
                }
            );
            $table->editColumn(
                'terminal',
                function ($row) {
                    $labels = [];
                    foreach ($row->terminals as $terminal) {
                        $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $terminal->name);
                    }
                    return implode(' ', $labels);
                }
            );
            //$table->editColumn('ship', function ($row) {
            //$labels = [];
            //foreach ($row->ships as $ship) {
            //$labels[] = sprintf($ship->name);
            //}
            //return implode(' ', $labels);
            //}
            //);
            $table->editColumn(
                'ship_id',
                function ($row) {
                    $labels = [];
                    foreach ($row->ships as $ship) {
                        $labels[] = sprintf($ship->ship_ids, $ship->name);
                        $labels[] = sprintf('- %s', $ship->name);
                    }
                    return implode(' ', $labels);
                }
            );
            $table->rawColumns(['actions', 'placeholder', 'roles', 'terminal']);
            return $table->make(true);
        }
        return view('admin.users.index');
    }

    public function create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $roles     = Role::whereNotIn('id', [1,2,4])->get()->pluck('title', 'id');
        $terminals = Terminal::all()->pluck('name', 'id');

        $userauth = auth()->user();
        $rolesauth = $userauth->roles->pluck('id')->toArray();

        if(in_array(1, $rolesauth, false)) {
            $ships = Ship::all();
            //as admin

        } elseif (in_array(4, $rolesauth, false)) {

            $distributor = Distributor::where('distributor_id', $userauth->id)->first();
            $ships = $distributor->distributor->ships;
            // $distributor->users()->sync($user->id);

        }
        //        $terminals = Terminal::whereNotIn('id',function($query) {
        //
        //            $query->select('terminal_id')->from('terminal_user');
        //
        //        })->get()->pluck('name', 'id');
        return view('admin.users.create', compact('roles', 'terminals', 'ships'));
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->all());
        if ($request->email) {
            foreach ($request->email as $email) {
                if($email) {
                    $emailUser          = new EmailUser();
                    $emailUser->email   = $email;
                    $emailUser->user_id = $user->id;
                    $emailUser->save();
                }
            }
        }
        $user->roles()->sync($request->input('roles', []));
        $user->terminals()->sync($request->input('terminals', []));
        $user->ships()->sync($request->input('ships', []));

        $userauth = auth()->user();
        $rolesauth = $userauth->roles->pluck('id')->toArray();

        if(in_array(1, $rolesauth, false)) {
            //as admin

        } elseif (in_array(4, $rolesauth, false)) {

            $distributor = Distributor::where('distributor_id', $userauth->id)->first();
            $distributor->users()->attach($user->id);
        }

        return redirect()->route('admin.users.index');
    }

    public function edit(User $user)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $roles     = Role::where('id', '!=', 2)->where('id', '!=', 1)->get()->pluck('title', 'id');

        //        $terminals = Terminal::whereNotIn('id',function($query) use ($user){
        //            $query->select('terminal_id')->from('terminal_user')->where('user_id', '!=',$user->id);
        //        })->get()->pluck('name', 'id');
        $terminals = Terminal::all()->pluck('name', 'id');
        $ships = Ship::all();

        $user->load('roles', 'terminals', 'email', 'ships');
        return view('admin.users.edit', compact('roles', 'user', 'terminals', 'ships'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->all());
        $user->roles()->sync($request->input('roles', []));
        $user->terminals()->sync($request->input('terminals', []));
        $user->ships()->sync($request->input('ships', []));
        if ($request->email) {
            EmailUser::where('user_id', $user->id)->delete();
            foreach ($request->email as $email) {
                if($email) {
                    $emailUser          = new EmailUser();
                    $emailUser->email   = $email;
                    $emailUser->user_id = $user->id;
                    $emailUser->save();
                }
            }
        }
        return redirect()->route('admin.users.index');
    }

    public function show(User $user)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user->load('roles', 'email', 'terminals', 'managerManagers', 'userManagers', 'ships');
        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user->delete();
        return back();
    }

    public function massDestroy(MassDestroyUserRequest $request)
    {
        User::whereIn('id', request('ids'))->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function changePassword()
    {
        $user = Auth::user();
        return view('admin.users.change-password', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function storePassword(Request $request)
    {
        $this->validate(
            $request,
            [
                'old_password' => 'required|check_password',
                'password' => 'required|min:6|confirmed',
            ]
        );
        $user = Auth::user();
        $request->merge(['password' => bcrypt($request->get('password'))]);
        $user->fill($request->except('_method', '_token'));
        $user->save();
        Session::flash('message', 'Password updated!');
        return back();
    }

    public function changeTimeZone()
    {
        $user = Auth::user();
        return view('admin.users.change-timezone', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function storeTimeZone(Request $request)
    {
        $user = Auth::user();
        $timezone = $request->get('timezone');
        $user->timezone = $timezone;
        $user->save();
        Session::flash('message', 'timezone updated!');
        return back();
    }

    public function queryIndexAjax()
    {
        $userauth = auth()->user();
        $rolesauth = $userauth->roles->pluck('id')->toArray();

        if(in_array(1, $rolesauth, false)) {

            $userId= [];

            $distributor = Distributor::all();
            foreach ($distributor as $item) {
                $userId = [...$userId, ...$item->users->pluck('id')];
            }
            // $usersid = $distributor->users->pluck('id');


            return User::with(['roles', 'email', 'terminals', 'ships'])
        ->join('role_user', 'users.id', '=', 'role_user.user_id')
        ->where('role_user.role_id', 3)
        ->whereNotIn('id', $userId)
        ->select(sprintf('%s.*', (new User())->table));

        } elseif (in_array(4, $rolesauth, false)) {

            $distributor = Distributor::where('distributor_id', $userauth->id)->first();
            $usersid = $distributor->users->pluck('id');

            return User::with(['roles', 'email', 'terminals', 'ships'])
        ->join('role_user', 'users.id', '=', 'role_user.user_id')
        ->where('role_user.role_id', 3)
        ->whereIn('id', $usersid)
        ->select(sprintf('%s.*', (new User())->table));

        }
    }

}

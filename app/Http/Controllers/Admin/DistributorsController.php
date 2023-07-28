<?php

namespace App\Http\Controllers\Admin;

use App\EmailUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyDistributorRequest;
use App\Http\Requests\StoreDistributorRequest;
use App\Http\Requests\UpdateDistributorRequest;
use App\Role;
use App\Terminal;
use App\Distributor;
use App\User;
use App\Manager;
use App\Ship;
use Gate;
use Illuminate\Http\Request;
use PharIo\Manifest\Email;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Auth;
use Session;

class DistributorsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Distributor::with(['distributor', 'managers', 'users' ])->select(sprintf('%s.*', (new Distributor())->table));
            $table = Datatables::of($query);
            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn(
                'actions',
                function ($row) {
                    $viewGate      = 'distributor_show';
                    $editGate      = 'distributor_edit';
                    $deleteGate    = 'distributor_delete';
                    $crudRoutePart = 'distributors';
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
                    return $row->distributor->id ? $row->distributor->id : "";
                }
            );
            $table->editColumn(
                'name',
                function ($row) {
                    return $row->distributor->name ? $row->distributor->name : "";
                }
            );
            $table->editColumn(
                'username',
                function ($row) {
                    return $row->distributor->username ? $row->distributor->username : "";
                }
            );
            $table->editColumn(
                'roles',
                function ($row) {
                    $labels = [];
                    foreach ($row->distributor->roles as $role) {
                        $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $role->title);
                    }
                    return implode(', ', $labels);
                }
            );
            $table->editColumn(
                'email',
                function ($row) {
                    $labels = [];
                    foreach ($row->distributor->email as $email) {
                        $labels[] = $email->email;
                    }
                    return implode(', ', $labels);
                }
            );

            $table->editColumn(
                'total',
                function ($row) {
                    return $row->distributor->ships->count();
                }
            );
            $table->editColumn(
                'terminal',
                function ($row) {
                    $labels = [];
                    foreach ($row->distributor->terminals as $terminal) {
                        $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $terminal->name);
                    }
                    return implode(' ', $labels);
                }
            );
            $table->editColumn(
                'ship_id',
                function ($row) {
                    $labels = [];
                    foreach ($row->distributor->ships as $ship) {
                        $labels[] = sprintf($ship->ship_ids, $ship->name);
                        $labels[] = sprintf('- %s', $ship->name);
                    }
                    return implode(' ', $labels);
                }
            );
            $table->rawColumns(['actions', 'placeholder', 'roles', 'terminal','email','total']);
            return $table->make(true);
        }

        $allship = Ship::orderBy('name', 'ASC')->get();

        $distributor = Distributor::with('distributor.ships')->get();
        $shipused = [];
        foreach ($distributor as $value) {
            $shipused = [...$value->distributor->ships->pluck('id')->toArray()];
        }

        return view('admin.distributors.index', [
            'terminal_free' => Ship::orderBy('name', 'ASC')->whereNotIn('id', $shipused)->get(),
            'terminal_used' => Ship::orderBy('name', 'ASC')->whereIn('id', $shipused)->get()
        ]);
    }

    public function create()
    {
        abort_if(Gate::denies('distributor_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $roles     = Role::whereNotIn('id', [1,2,3])->get()->pluck('title', 'id');
        $terminals = Terminal::all()->pluck('name', 'id');
        $ships = Ship::all();
        return view('admin.distributors.create', compact('roles', 'terminals', 'ships'));
    }

    public function store(StoreDistributorRequest $request)
    {
        $user = User::create($request->all());
        $user->roles()->sync(4);
        $distributor             = new Distributor();
        $distributor->distributor_id = $user->id;
        $distributor->save();

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

        $distributor->distributor->terminals()->sync($request->input('terminals', []));
        $distributor->distributor->ships()->sync($request->input('ships', []));

        return redirect()->route('admin.distributors.index');
    }

    public function edit(Distributor $distributor)
    {
        abort_if(Gate::denies('distributor_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $roles     = Role::whereNotIn('id', [1,2,3])->get()->pluck('title', 'id');

        $terminals = Terminal::all()->pluck('name', 'id');
        $ships = Ship::all();

        $distributor->distributor->load('roles', 'terminals', 'email', 'ships');
        return view('admin.distributors.edit', compact('roles', 'distributor', 'terminals', 'ships'));
    }

    public function update(UpdateDistributorRequest $request, Distributor $distributor)
    {
        $user = $distributor->distributor;

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
        return redirect()->route('admin.distributors.index');
    }

    public function show(Distributor $distributor)
    {
        abort_if(Gate::denies('distributor_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $distributor->distributor->load('roles', 'email', 'terminals', 'managerManagers', 'userManagers', 'ships');
        return view('admin.distributors.show', ['user' => $distributor->distributor]);
    }

    public function destroy(Distributor $distributor)
    {
        abort_if(Gate::denies('distributor_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $distributor->delete();
        return back();
    }

    public function massDestroy(MassDestroyDistributorRequest $request)
    {
        Distributor::whereIn('id', request('ids'))->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function changePassword()
    {
        $user = Auth::user();
        return view('admin.distributors.change-password', compact('user'));
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
        return view('admin.distributors.change-timezone', compact('user'));
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
}

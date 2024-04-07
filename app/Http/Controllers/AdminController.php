<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::count();
        $customers = Customer::count();
        $categories = Category::count();
        $artworks = Artwork::count();
        $events = Event::count();
        $orders = Order::count();
        $tickets = Ticket::count();

        return response()->json([
            'status' => true,
            'data' => [
                'users' => $users,
                'customers' => $customers,
                'categories' => $categories,
                'artworks' => $artworks,
                'events' => $events,
                'orders' => $orders,
                'tickets' => $tickets,
            ]]);
    }

    public function users()
    {
        $users = User::orderBy('name')->get();

        $response['status'] = true;
        $response['data'] = $users;

        return response()->json($response);
    }

    public function customers()
    {
        $customers = Customer::orderBy('name')->get();

        return response()->json(['status' => true, 'message' => 'Get customers success.', 'data' => $customers]);
    }

    public function categories()
    {
        $categories = Category::all();

        $response['status'] = true;
        $response['data'] = $categories;

        return response()->json($response);
    }

    public function artworks()
    {
        $artworks = Artwork::all();

        $response['status'] = true;
        $response['data'] = $artworks;

        return response()->json($response);
    }

    public function events()
    {
        $events = Event::with('category:id,name')->orderBy('name')->get();

        $response['status'] = true;
        $response['data'] = $events;

        return response()->json($response);
    }

    public function orders()
    {
        $orders = Order::with(['event:id,name', 'user:id,name', 'customer:id,name'])->get();

        $response['status'] = true;
        $response['data'] = $orders;

        return response()->json($response);
    }

    public function tickets()
    {
        $tickets = Ticket::all();

        $response['status'] = true;
        $response['data'] = $tickets;

        return response()->json($response);
    }

    public function param()
    {
        $param = request('name');
        $customer = Customer::where('name', 'like', "%{$param}%")->get();

        return response()->json(['data' => $customer]);
    }

    public function test()
    {
        // $customers = User::all();

        return response()->json(['status' => true, 'data' => "ngetes aja"]);
    }
}

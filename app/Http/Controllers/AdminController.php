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
        $users = User::all()->count();
        $customers = Customer::all()->count();
        $categories = Category::all()->count();
        $artworks = Artwork::all()->count();
        $events = Event::all()->count();
        $orders = Order::all()->count();
        $tickets = Ticket::all()->count();

        $response['status'] = true;
        $response['data'] = [
            'users' => $users,
            'customers' => $customers,
            'categories' => $categories,
            'artworks' => $artworks,
            'events' => $events,
            'orders' => $orders,
            'tickets' => $tickets,
        ];

        return response()->json($response);
    }

    public function users()
    {
        $users = User::all();

        $response['status'] = true;
        $response['data'] = $users;

        return response()->json($response);
    }

    public function customers()
    {
        $customers = Customer::all();

        $response['status'] = true;
        $response['data'] = $customers;

        return response()->json($response);
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
        $events = Event::with('categories:id,name')->get();

        $response['status'] = true;
        $response['data'] = $events;

        return response()->json($response);
    }

    public function orders()
    {
        $orders = Order::all();

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
}

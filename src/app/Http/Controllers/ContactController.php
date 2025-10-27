<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use App\Models\Category;

class ContactController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view("index", compact('categories'));
    }

    public function confirm(ContactRequest $request)
    {
        $contact = $request->only(['category_id', 'last_name', 'first_name', 'gender', 'email', 'tel', 'tel1', 'tel2', 'tel3', 'address', 'building', 'detail']);
        return view('confirm', compact('contact'));
    }
    public function thanks(Request $request)
    {
        $contact = $request->only(['category_id', 'last_name', 'first_name', 'gender', 'email', 'tel', 'address', 'building', 'detail']);
        Contact::create($contact);
        return view('thanks');
    }
    public function contactBack(Request $request)
    {
        $contact = $request->only(['category_id', 'last_name', 'first_name', 'gender', 'email', 'tel1', 'tel2', 'tel3', 'address', 'building', 'detail']);
        //return view("index", compact('contact', 'categories'));
        return redirect('/')->withInput($request->all());
    }
    public function store(Request $request)
    {
        $contact = $request->only(['category_id', 'last_name', 'first_name', 'gender', 'email', 'tel', 'address', 'building', 'detail']);
        Contact::create($contact);
        //return view("index", compact('contact', 'categories'));
        return view('/thanks');
    }
}

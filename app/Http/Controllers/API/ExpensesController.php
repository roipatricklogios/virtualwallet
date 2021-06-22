<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Expense;

class ExpensesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Expense::latest()->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'expense_category' => 'required|string|max:191',
            'amount' => 'required',
            'trans_flow' => 'required',
            'entry_date' => 'required|date'
        ]);
        return Expense::create([
            'user_id' => auth('api')->user()->id,
            'expense_category' => $request['expense_category'],
            
            'amount' => $request['amount'],
            'trans_flow' => $request['trans_flow'],
            'entry_date' => $request['entry_date'],
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $this->validate($request, [
            'expense_category' => 'required|string|max:191',
            'amount' => 'required',
            'entry_date' => 'required|date'
        ]);
        
        $expense->update($request->all());
        return ['message' => 'Updating'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);

        $expense->delete();

        return ['message' => 'Deleting'];
    }
}

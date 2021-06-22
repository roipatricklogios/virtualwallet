<template>
    <div class="container" style="max-width: 100%; padding: 0;margin: 0">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="content-header card-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">Expenses</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">My Wallet</a></li>
                            <li class="breadcrumb-item"><a href="#">Expenses</a></li>
                            </ol>
                        </div>
                        </div>
                    </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                <div class="card-header">
                                    <div class="card-tools">
                                        <button class="btn btn-success" @click="newModal">
                                            Add New Expense
                                            <i class="fas fa-user-plus fa-fw"></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>Expense Category</th>
                                            <th>Amount</th>
                                            <th>Transaction Flow</th>
                                            <th>Entry Date</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="expense in expenses" :key="expense.id" @click="editModal(expense)">
                                            <td>{{ expense.expense_category | upText }}</td>
                                            <td>{{ formatPrice(expense.amount) }}</td>
                                            <td>{{ expense.trans_type == 0 ? 'Outgoing' : 'Incoming' }}</td>
                                            <td>{{ expense.entry_date }}</td>
                                            <td>{{ expense.created_at | myDate }}</td>
                                        </tr>
                                    </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNewModal" tabindex="-1" role="dialog" aria-labelledby="addNewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" v-show="!editmode" id="addNewModalLabel">Add Expense</h5>
                    <h5 class="modal-title" v-show="editmode" id="addNewModalLabel">Update Expense</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="editmode ? updateExpense() : createExpense()">
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Expense Category</label>
                                </div>
                                <div class="col-md-8">
                                    <select name="expense_category" v-model="form.expense_category" class="form-control">
                                        <option v-for="(cat, index) in cats" :value="cat.display_name"
                                                v-bind:key="index">{{ cat.display_name | upText }}
                                        </option>
                                    </select>
                                    <has-error :form="form" field="expense_category"></has-error>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Amount</label>
                                </div>
                                <div class="col-md-8">
                                    <input v-model="form.amount" type="number" name="amount"
                                        placeholder="Amount"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('amount') }">
                                    <has-error :form="form" field="amount"></has-error>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Transaction Flow</label>
                                </div>
                                <div class="col-md-8">
                                    <select name="trans_flow" v-model="form.trans_flow" class="form-control">
                                        <option value="0">Outgoing</option>
                                        <option value="1">Incoming</option>
                                    </select>

                                    
                                    <has-error :form="form" field="amount"></has-error>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Entry Date</label>
                                </div>
                                <div class="col-md-8">
                                    <input v-model="form.entry_date" type="date" name="entry_date"
                                        placeholder=" /  /  "
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('entry_date') }">
                                    <has-error :form="form" field="entry_date"></has-error>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button @click="deleteCategories()" v-show="editmode" type="button" class="btn btn-danger" style="position: absolute; left: 15px;">Delete</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                        <button v-show="!editmode" type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
                </div>
            </div>
        </div>


    </div>
</template>

<script>
    export default {
        data(){
            return {
                editmode: false,
                expenses: {},
                cats: {},
                form: new Form({
                    id : '',
                    user_id : '',
                    expense_category : '',
                    amount : '',
                    entry_date : ''
                })
            }
        },
        methods: {
            formatPrice(value) {
                let val = (value/1).toFixed(2).replace('.', '.')
                return '$'+ val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            },
            updateExpense(){
                this.$Progress.start();
                this.form.put('api/expenses/'+this.form.id)
                .then(() => {
                    $('#addNewModal').modal('hide');
                    Toast.fire({
                        icon: 'success',
                        title: 'Information Updated'
                    })
                    this.$Progress.finish();
                    Fire.$emit('AfterCreate');
                })
                .catch(() => {
                    this.$Progress.fail();
                });
            },
            deleteCategories(){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {

                        //Ajax Here
                        if(result.value){
                            this.form.delete('api/expenses/'+this.form.id)
                            .then(() => {
                                Swal.fire(
                                    'Deleted!',
                                    'Expense has been deleted.',
                                    'success'
                                )
                                $('#addNewModal').modal('hide');
                                Fire.$emit('AfterCreate');
                            })
                            .catch(() => {
                                Swal.fire(
                                    'Failed!',
                                    'Something is Wrong.',
                                    'warning'
                                )
                            });
                        }
                        
                })
            },
            editModal(expense){
                this.loadCategories();
                console.log(expense);
                this.editmode = true;
                this.form.reset();
                $('#addNewModal').modal('show');
                this.form.fill(expense);
            },
            newModal(){
                this.editmode = false;
                this.form.reset();
                this.loadCategories();
                $('#addNewModal').modal('show');
            },
            loadCategories(){
                axios.get("api/categories")
                .then(
                    ({ data }) => (this.cats = data.data)
                );
            },
            loadExpenses(){
                axios.get("api/expenses").then(({ data }) => (this.expenses = data.data));
            },
            createExpense(){
                this.$Progress.start();
                this.form.post('api/expenses')
                    .then(() => {
                        Fire.$emit('AfterCreate');
                        $('#addNewModal').modal('hide');
                        Toast.fire({
                            icon: 'success',
                            title: 'Expenses Added successfully'
                        })
                        this.$Progress.finish();
                    })
                    .catch(() => {

                    })
            }
        },
        created() {
            // console.log('Component mounted.')
            this.loadExpenses();
            Fire.$on('AfterCreate',() => {
                this.loadExpenses();
            });
            // setInterval(() => this.loadExpenses(), 5000);
        }
    }
</script>

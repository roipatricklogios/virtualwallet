<template>
    <div class="container" style="max-width: 100%; padding: 0;margin: 0">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="content-header card-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">Expense Categories</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">My Wallet</a></li>
                            <li class="breadcrumb-item"><a href="#">Expense Categories</a></li>
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
                                            Add Category
                                            <i class="fas fa-user-plus fa-fw"></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>Displayed Name</th>
                                            <th>Description</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="cat in cats" :key="cat.id" @click="editModal(cat)">
                                            <td>{{ cat.display_name | upText }}</td>
                                            <td>{{ cat.description | upText }}</td>
                                            <td>{{ cat.created_at | myDate }}</td>
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
                    <h5 class="modal-title" v-show="!editmode" id="addNewModalLabel">Add Category</h5>
                    <h5 class="modal-title" v-show="editmode" id="addNewModalLabel">Update Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="editmode ? updateCategories() : createCategories()">
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Display Name</label>
                                </div>
                                <div class="col-md-8">
                                    <input v-model="form.display_name" type="text" name="display_name"
                                        placeholder="Display Name"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('display_name') }">
                                    <has-error :form="form" field="display_name"></has-error>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Description</label>
                                </div>
                                <div class="col-md-8">
                                    <input v-model="form.description" type="text" name="description"
                                        placeholder="Description"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('description') }">
                                    <has-error :form="form" field="description"></has-error>
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
                cats: {},
                form: new Form({
                    id : '',
                    display_name : '',
                    description : ''
                })
            }
        },
        methods: {
            updateCategories(){
                this.$Progress.start();
                this.form.put('api/categories/'+this.form.id)
                .then(() => {
                    $('#addNewModal').modal('hide');
                    Toast.fire({
                        icon: 'success',
                        title: 'Category Updated'
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
                            this.form.delete('api/categories/'+this.form.id)
                            .then(() => {
                                Swal.fire(
                                    'Deleted!',
                                    'Category has been deleted.',
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
            editModal(cat){
                console.log(cat);
                this.editmode = true;
                this.form.reset();
                $('#addNewModal').modal('show');
                this.form.fill(cat);
            },
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNewModal').modal('show');
            },
            loadCategories(){
                axios.get("api/categories").then(({ data }) => (this.cats = data.data));
            },
            createCategories(){
                this.$Progress.start();
                this.form.post('api/categories')
                    .then(() => {
                        Fire.$emit('AfterCreate');
                        $('#addNewModal').modal('hide');
                        Toast.fire({
                            icon: 'success',
                            title: 'Category Created successfully'
                        })
                        this.$Progress.finish();
                    })
                    .catch(() => {

                    })
            }
        },
        created() {
            // console.log('Component mounted.')
            this.loadCategories();
            Fire.$on('AfterCreate',() => {
                this.loadCategories();
            });
            // setInterval(() => this.loadCategories(), 5000);
        }
    }
</script>

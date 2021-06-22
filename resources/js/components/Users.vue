<template>
    <div class="container" style="max-width: 100%; padding: 0;margin: 0">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="content-header card-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">User</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">User Management</a></li>
                            <li class="breadcrumb-item"><a href="#">Users</a></li>
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
                                            Add New Users
                                            <i class="fas fa-user-plus fa-fw"></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email Address</th>
                                            <th>Role</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="user in users" :key="user.id" @click="editModal(user)">
                                            <td>{{user.id}}</td>
                                            <td>{{user.name}}</td>
                                            <td>{{user.email}}</td>
                                            <td>{{user.type | upText}}</td>
                                            <td>{{user.created_at | myDate}}</td>
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
                <h5 class="modal-title" v-show="!editmode" id="addNewModalLabel">Add User</h5>
                <h5 class="modal-title" v-show="editmode" id="addNewModalLabel">Update User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form @submit.prevent="editmode ? updateUser() : createUser()">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Name</label>
                            </div>
                            <div class="col-md-8">
                                <input v-model="form.name" type="text" name="name"
                                    placeholder="Name"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                <has-error :form="form" field="name"></has-error>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Email Address</label>
                            </div>
                            <div class="col-md-8">
                                <input v-model="form.email" type="email" name="email"
                                    placeholder="Email Address"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                <has-error :form="form" field="email"></has-error>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" v-show="typemode">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Password</label>
                            </div>
                            <div class="col-md-8">
                                <input v-model="form.password" type="password" name="password"
                                    placeholder="Password"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                                <has-error :form="form" field="password"></has-error>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" v-show="!typemode">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Role</label>
                            </div>
                            <div class="col-md-8">
                                <select name="type" v-model="form.type" class="form-control">
                                    <option v-for="(role, index) in roles" :value="role.display_name | lowText"
                                            v-bind:key="index">{{ role.display_name | upText }}
                                    </option>
                                </select>
                                <has-error :form="form" field="type"></has-error>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="deleteUser()" v-show="editmode" type="button" class="btn btn-danger" style="position: absolute; left: 15px;">Delete</button>
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
                typemode: false,
                user_type : document.querySelector("meta[name='user-type']").getAttribute('content'),
                users: {},
                roles: {},
                drpdown:{
                    display_name: '',
                    description: ''
                },
                form: new Form({
                    id : '',
                    name : '',
                    password: '',
                    email : '',
                    type : ''
                })
            }
        },
        methods: {
            updateUser(){
                this.$Progress.start();
                this.form.put('api/createuser/'+this.form.id)
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
            deleteUser(){
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
                            this.form.delete('api/createuser/'+this.form.id)
                            .then(() => {
                                Swal.fire(
                                    'Deleted!',
                                    'User has been deleted.',
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
            editModal(user){
                this.loadRoles();
                if(user.type != 'administrator'){
                    if(this.user_type != 'administrator'){
                        this.typemode = true;
                    }else{
                        this.typemode = false;
                    }
                    this.editmode = true;
                    this.form.reset();
                    $('#addNewModal').modal('show');
                    this.form.fill(user);
                    console.log(user);
                }else{
                    Swal.fire(
                        'Warning!',
                        'Admin Role Cannot be Updated.',
                        'warning'
                    )
                }
            },
            newModal(){
                this.editmode = false;
                this.form.reset();
                this.loadRoles();
                $('#addNewModal').modal('show');
            },
            loadRoles(){
                axios.get("api/roles")
                .then(
                    ({ data }) => (this.roles = data.data)
                );
            },
            loadUsers(){
                axios.get("api/createuser").then(({ data }) => (this.users = data.data));
                
            },
            createUser(){
                this.$Progress.start();
                this.form.post('api/createuser')
                    .then(() => {
                        Fire.$emit('AfterCreate');
                        $('#addNewModal').modal('hide');
                        Toast.fire({
                            icon: 'success',
                            title: 'User Added successfully'
                        })
                        this.$Progress.finish();
                    })
                    .catch(() => {

                    })
            }
        },
        created() {
            // console.log('Component mounted.')
            this.loadUsers();
            Fire.$on('AfterCreate',() => {
                this.loadUsers();
            });
            
            // setInterval(() => this.loadUsers(), 5000);
        }
    }
</script>

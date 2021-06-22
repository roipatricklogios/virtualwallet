<template>
    <div class="container" style="max-width: 100%; padding: 0;margin: 0">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="content-header card-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">My Expenses</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            </ol>
                        </div>
                        </div>
                    </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <table class="table table-hover text-nowrap" id="expense_table">
                                    <thead>
                                        <tr>
                                            <th>Expense Categories</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="col-md-8">
                                <pie-chart v-if="loaded" :chartdata="chartdata" :options="options"></pie-chart>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import PieChart from './Chart.vue'
    import axios from 'axios';

    export default {
        components: { 
            PieChart
        },
        data:() => ({
            chartdata : null,
            options : null,
            loaded : false,
            expdata: {},
        }),
        methods: {
            loadUsers(){
                axios.get("api/chart").then(({ data }) => (this.expdata = data.data));
                console.log(this.expdata);
            }
        },
        async mounted(){
            this.loaded = false
            await axios.get('/api/chart').then((resdata) => {
                console.log(resdata.data);

                var labelsArray = [],
                    dataArray = [];

                $.each(resdata.data, function(key, values) {
                    console.log(values);
                    $('#expense_table tbody').append(
                        '<tr>'+
                        '<td class="cat-td">'+values['expense_category']+'</td>'+
                        '<td>'+values['Total']+'</td>'+
                        '</tr>'
                    );
                    $.each(values, function(k, v) {
                        if(k == 'expense_category'){
                            labelsArray.push(v);
                        }

                        if(k == 'Total'){
                            dataArray.push(v);
                        }
                    });
                });
                
                $('.cat-td').css('textTransform', 'capitalize');

                console.log(labelsArray);

                this.loaded = true;
                this.chartdata = {
                    labels: labelsArray,
                    datasets:[
                        {
                            label: 'My Expenses',
                            data: dataArray,
                            borderColor: [
                                'rgba(255,99,132,1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)'            
                            ],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',                
                            ],
                            borderWidth: 1
                        }
                    ]
                },
                this.options = {
                    legend: {
                        display: true
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            }).catch((err) => {
                console.log(err);
            });
        },
        created() {
            this.loadUsers();
        },
    }
</script>

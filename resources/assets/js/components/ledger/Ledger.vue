<template>
    <div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-5">
                        <div class="form-group">
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                                <input type="text" class="form-control pull-right ledger-date" id="from_date" placeholder="From">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                                <input type="text" class="form-control pull-right ledger-date" id="to_date" placeholder="To">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <div class="input-group">
                                <select class="form-control pull-right" v-model="entry_type">
                                    <option></option>
                                    <option value="debit">Debits</option>
                                    <option value="credit">Credits</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <button class = "btn btn-primary" type = "button">
                                <span class="glyphicon glyphicon-filter"></span> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-borderless table-hover table-responsive ledger-table table-striped">
                            <thead>
                            <tr>
                                <th>From</th>
                                <th>To</th>
                                <th>Amount</th>
                                <th>Reason</th>
                                <th>Transaction Type</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody id="ledger-body">
                            <tr style="font-weight: bold;" v-for="entry in entries" data-toggle="tooltip" :title="entry.from_now" :class="{'text-success': entry.debit, 'text-danger': entry.credit}">
                                <td style="vertical-align: middle;">{{ entry.money_from }}</td>
                                <td style="vertical-align: middle;">{{ entry.money_to }}</td>
                                <td style="vertical-align: middle;">{{ entry.amount }}</td>
                                <td style="vertical-align: middle;">{{ entry.reason }}</td>
                                <td style="vertical-align: middle;">{{ entry.type }}</td>
                                <td style="vertical-align: middle;">{{ entry.date }}</td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>From</th>
                                <th>To</th>
                                <th>Amount</th>
                                <th>Reason</th>
                                <th>Transaction Type</th>
                                <th>Date</th>
                            </tr>
                            </tfoot>
                        </table>
                        <ul class = "pager">
                            <li class = "previous { disabled: hasOffset }"><a @click="getPrevious">&larr; Newer</a></li>
                            <li class = "next { disabled: hasEntries }"><a @click="getNext">Older &rarr;</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style>

</style>
<script>
    export default{
        data(){
            return{
                title:'Transactions',
                entries: [],
                from_date: '',
                to_date: '',
                entry_type: '',
                offset: 0
            }
        },
        computed:  {
            hasEntries: function() {
                return this.entries.length > 0
            },
            hasOffset: function() {
                return this.offset > 0
            }
        },
        /**
         * Prepare the component (Vue 1.x).
         */
        ready() {
            this.prepareComponent()
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.prepareComponent()
        },
        methods: {
            prepareComponent() {
                this.getEntries()
                $("#from_date").on('change', ()=>{
                    this.from_date = $("#from_date").val()
                })

                $("#to_date").on('change', ()=>{
                    this.to_date = $("#to_date").val()
                })
            },
            getNext() {
                if (this.entries.length){
                    this.offset += 10
                    try {
                        this.getEntries(this.getOptions())
                    }catch(e) {
                        console.log(e)
                    }
                }
            },
            getPrevious() {
                if (this.offset && (this.offset - 10) >= 0){
                    this.offset -= 10
                    try {
                        this.getEntries(this.getOptions())
                    }catch(e) {
                        console.log(e)
                    }
                }
            },
            getOptions() {
                var options = {offset: 0}
                if (this.offset){
                    options["offset"] = this.offset
                }

                if (this.entry_type){
                    options["entry_type"] = this.entry_type
                }

                if (this.from_date && this.to_date)
                {
                    var from_date = moment(this.reorderTime(this.from_date)).add(this.getUtcOffset(moment(this.reorderTime(this.from_date))), 'm')
                    var to_date = moment(this.reorderTime(this.to_date)).add(this.getUtcOffset(moment(this.reorderTime(this.to_date))), 'm')
                    var days_from_date = null
                    if (from_date > to_date) {
                        days_from_date = from_date.diff(to_date, 'days')
                        options["from_date"] = this.reorderTime(this.from_date)
                    }else{
                        days_from_date = to_date.diff(from_date, 'days')
                        options["from_date"] = this.reorderTime(this.to_date)
                    }
                    options["days_from_date"] = days_from_date
                }

                console.log(options)
                return options
            },
            reorderTime(time){
                return (time).split('/').reverse().join('-')
            },
            getUtcOffset(time) {
                return time.utcOffset()
            },
            urlEncodeOptions(options) {
                var uri = ''
                for(var i in options) {
                    uri += i + '=' + options[i]
                }
                return uri
            },
            getEntries(options = {offset: 0}) {
                axios.get('/ledger?' + this.urlEncodeOptions(options))
                        .then(response => {
                            this.humanizeEntries(response.data.data)
                        });
            },
            humanizeEntries(data){
                console.log(JSON.stringify(data))
                var len = data.length
                for(var i = 0; i < len; i++)
                {
                    data[i]["type"] = data[i].debit ? 'Debit' : 'Credit'
                    var time = data[i]["created_at"]
                    var offset = moment(time).utcOffset()
                    data[i]["date"] = moment(time).add(offset, 'm').format("dddd, MMMM Do YYYY, h:mm:ss a")
                    data[i]["from_now"] = moment(time).add(offset, 'm').fromNow()

                    if (data[i].debit){
                        data[i].money_to = data[i].name
                    }else{
                        data[i].money_from = data[i].name
                    }
                }
                this.entries = data
            }
        }
    }
</script>

<template>
    <el-dialog   :visible="showDialog"  @open="create" @opened="opened" width="60%" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false">
        <Keypress key-event="keyup" :key-code="13" @success="someMethod" />
        <span slot="title">
            <div class="widget-summary widget-summary-xs pl-3 p-2">
                <div class="widget-summary-col widget-summary-col-icon">
                    <div class="summary-icon bg-success">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
                <div class="widget-summary-col">
                    <div class="summary row">
                        <div class="col-md-6">
                            <h4 class="title">Venta exitosa : comprobante {{form.number}}</h4>
                        </div>
                        <div class="col-md-6">
                            <h4 class="title">Estado de comprobante: {{ (statusDocument.sent) ? 'Enviado a Sunat':'No enviado a Sunat'}}</h4>
                            <h4 class="title">Envio automático: {{ (configuration.send_auto) ? 'Activado':'Desactivado'}}</h4>

                        </div>
                    </div>
                </div>
            </div>
        </span>
        <div class="form-body el-dialog__body_custom">
            <div class="row">
                <div class="col-md-12 m-bottom">
                    <el-tabs v-model="activeName">
                        <el-tab-pane label="Imprimir Ticket" name="first" v-if="config !== null  && config.show_ticket_80">
                            <embed v-if="config !== null  && config.show_ticket_80" id="nemo" :src="form.print_ticket" type="application/pdf" width="100%" height="450px"/>
                        </el-tab-pane>
                        <el-tab-pane label="Imprimir Ticket 58" name="second" v-if="config.show_ticket_58">
                            <embed v-if="config.show_ticket_58" :src="form.print_ticket_58" type="application/pdf" width="100%" height="450px"/>
                        </el-tab-pane>
                        <el-tab-pane label="Imprimir Ticket 50" name="third" v-if="config.show_ticket_50">
                            <embed v-if="config.show_ticket_50" :src="form.print_ticket_50" type="application/pdf" width="100%" height="450px"/>
                        </el-tab-pane>
                        <el-tab-pane label="Imprimir A4" name="quarter">
                            <embed :src="form.print_a4" type="application/pdf" width="100%" height="450px"/>
                        </el-tab-pane>
                        <el-tab-pane label="Imprimir A5" name="fifth">
                            <embed :src="form.print_a5" type="application/pdf" width="100%" height="450px"/>
                        </el-tab-pane>
                    </el-tabs>
                </div>
                <div class="col-md-12 d-sm-block d-md-block d-lg-none">
                    <div class="row">
                        <div class="col text-center font-weight-bold mt-3">
                            <button class="btn btn-lg btn-info waves-effect waves-light"
                                        type="button"
                                        @click="clickPrint(form.print_a4)">
                                    <i class="fa fa-file-alt"></i>
                            </button>
                            <p>A4</p>
                        </div>
                        <div
                            v-if="config !== null  && config.show_ticket_80"
                            class="col text-center font-weight-bold mt-3">
                            <button class="btn btn-lg btn-info waves-effect waves-light"
                                    type="button"
                                    @click="clickPrint(form.print_ticket)">
                                <i class="fa fa-receipt"></i>
                            </button>
                            <p>Ticket</p>
                        </div>
                        <div
                            v-if="config.show_ticket_58"
                            class="col text-center font-weight-bold mt-3">
                            <button class="btn btn-lg btn-info waves-effect waves-light"
                                    type="button"
                                    @click="clickPrint(form.print_ticket_58)">
                                <i class="fa fa-receipt"></i>
                            </button>
                            <p>Ticket 58</p>
                        </div>
                        <div
                            v-if="config.show_ticket_50"
                            class="col text-center font-weight-bold mt-3">
                            <button class="btn btn-lg btn-info waves-effect waves-light"
                                    type="button"
                                    @click="clickPrint(form.print_ticket_50)">
                                <i class="fa fa-receipt"></i>
                            </button>
                            <p>Ticket 50</p>
                        </div>
                        <div class="col text-center font-weight-bold mt-3">
                            <button class="btn btn-lg btn-info waves-effect waves-light"
                                    type="button"
                                    @click="clickPrint(form.print_a5)">
                                <i class="fa fa-file-alt"></i>
                            </button>
                            <p>A5</p>
                        </div>
                    </div>
                </div>
                <div class="row col-md-12">
                    <div class="col-md-6">
                        <el-input v-model="form.customer_email" ref="ref_customer_email" @keyup.native="keyupCustomerEmail">
                            <el-button slot="append" icon="el-icon-message"   @click="clickSendEmail" :loading="loading">Enviar</el-button>
                        </el-input>
                        <!-- <small class="form-control-feedback" v-if="errors.customer_email" v-text="errors.customer_email[0]"></small> -->

                    </div>

                    <div class="col-md-6">
                        <el-input v-model="form.customer_telephone">
                            <template slot="prepend">+51</template>
                                <el-button slot="append" @click="clickSendWhatsapp" >Enviar
                                    <el-tooltip class="item" effect="dark"  content="Es necesario tener aperturado Whatsapp web" placement="top-start">
                                        <i class="fab fa-whatsapp" ></i>
                                    </el-tooltip>
                                </el-button>
                        </el-input>
                    </div>

                    <div class="col-md-6 mt-4">
                    </div>
                    <div class="col-md-6 mt-4">
                        <el-button  type="primary"  class="float-right" @click="clickNewSale">Nueva venta</el-button>
                    </div>
                </div>

            </div>
        </div>
    </el-dialog>
</template>

<script>
    import {mapState, mapActions} from "vuex/dist/vuex.mjs";
    import Keypress from 'vue-keypress'
    export default {
        props: ['showDialog', 'recordId', 'statusDocument','resource'],
        components: {
           Keypress
        },
        data() {
            return {
                titleDialog: null,
                loading: false,
                errors: {},
                form: {},
                company: {},
                configuration: {},
                activeName: 'first',

            }
        },
        created() {
            this.initForm()
            this.loadConfiguration();
            /*
            this.$http.get(`/pos/status_configuration`).then(response => {
                this.$store.commit('setConfiguration', response.data)
            });
            */
        },
        mounted(){
        },
        computed: {
            ...mapState([
                'config',
            ]),
        },
        methods: {
            ...mapActions(['loadConfiguration']),
            clickSendWhatsapp() {

                if(!this.form.customer_telephone){
                    return this.$message.error('El número es obligatorio')
                }

                window.open(`https://wa.me/51${this.form.customer_telephone}?text=${this.form.message_text}`, '_blank');

            },
            someMethod(response){

                if(!this.showDialog)
                {
                    return
                }

                const external_id = this.form.external_id

                let format = 'a4'

                switch(this.activeName)
                {
                    case 'first':
                        format = 'ticket'
                        break;
                    case 'second':
                        format = 'ticket_58'
                        break;
                    case 'third':
                        format = 'ticket_50'
                        break;
                    case 'quarter':
                        format = 'a4'
                        break;
                    case 'fifth':
                        format= 'a5'
                        break;
                }


                if(this.resource == 'sale-notes')
                {
                    window.open(`/sale-notes/downloadExternal/${external_id}/${format}`, '_blank');
                }
                else if(this.resource == 'documents')
                {
                    if(format=='ticket'){
                        window.open(`/downloads/Document/${type}/${external_id}/pdf`, '_blank');
                    }else{
                        window.open(`downloads/documents/${type}/${external_id}/${format}`, '_blank');
                    }
                }

            },
            keyupCustomerEmail(e){
                if(e.keyCode === 9){
                    this.clickNewSale()
                }
                // console.log(e.keyCode)
            },
            initFocus(){
                this.$refs.ref_customer_email.$el.getElementsByTagName('input')[0].focus()
            },
            async clickNewSale(){

                await this.initForm()
                await this.$eventHub.$emit('cancelSale')
                await this.$eventHub.$emit('cancelSaleGarage')
                this.$emit('update:showDialog', false)

            },
            initForm() {
                this.errors = {};
                this.configuration = {};
                this.form = {
                    customer_email: null,
                    download_pdf: null,
                    print_a4: null,
                    print_a5: null,
                    print_ticket: null,
                    print_ticket_50: null,
                    print_ticket_58: null,
                    external_id: null,
                    number: null,
                    customer_telephone:null,
                    message_text:null,
                    id: null
                }

                this.changeActiveName();
            },
            create() {
                this.$http.get(`/${this.resource}/record/${this.recordId}`).then(response => {
                    this.form = response.data.data;
                    this.titleDialog = 'Comprobante: '+this.form.number;
                });

                this.$http.get(`/pos/status_configuration`).then(response => {
                    this.configuration = response.data
                });


            },
            opened(){
                this.initFocus()
            },
            clickSendEmail() {

                if(this.form.customer_email == null || this.form.customer_email == '') return this.$message.error('Ingrese el correo')
                this.loading = true
                this.$http.post(`/${this.resource}/email`, {
                    customer_email: this.form.customer_email,
                    id: this.form.id
                })
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success('El correo fue enviado satisfactoriamente')
                        } else {
                            this.$message.error('Error al enviar el correo')
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors
                        } else {
                            this.$message.error(error.response.data.message)
                        }
                    })
                    .then(() => {
                        this.loading = false
                    })
            },
            clickPrint(url) {
                window.open(`${url}`, '_blank');
            },
            changeActiveName() {
                this.loadConfiguration();
                this.activeName =( this.config!== null && this.config.show_ticket_80) ? 'first' : 'quarter';
                if((!this.config.show_ticket_80&&this.config.show_ticket_50)||(!this.config.show_ticket_80&&!this.config.show_ticket_50)){
                    this.activeName = ( this.config!== null &&this.config.show_ticket_58) ? 'second' : 'third';
                }
                if(!this.config.show_ticket_58&&!this.config.show_ticket_80){
                    this.activeName = ( this.config!== null &&this.config.show_ticket_50) ? 'third' : 'quarter';
                }
            }
            // clickConsultCdr(document_id) {
            //     this.$http.get(`/${this.resource}/consult_cdr/${document_id}`)
            //         .then(response => {
            //             if (response.data.success) {
            //                 this.$message.success(response.data.message)
            //                 this.$eventHub.$emit('reloadData')
            //             } else {
            //                 this.$message.error(response.data.message)
            //             }
            //         })
            //         .catch(error => {
            //             this.$message.error(error.response.data.message)
            //         })
            // },
            // clickFinalize() {
            //     location.href = (this.isContingency) ? `/contingencies` : `/${this.resource}`
            // },
            // clickNewDocument() {
            //     this.clickClose()
            // },
            // clickClose() {
            //     this.$emit('update:showDialog', false)
            //     this.initForm()
            // },
        }
    }
</script>

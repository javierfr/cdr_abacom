"use strict";(self.webpackChunkmantis_free_version=self.webpackChunkmantis_free_version||[]).push([[767],{7767:(h,c,r)=>{r.r(c),r.d(c,{default:()=>d});var s=r(177),e=r(3953),a=r(1626);let l=(()=>{class i{constructor(t){this.http=t,this.apiUrl="https://cdr.abacom.mx/api/sincronizar/uploadExcel"}uploadExcel(t){const n=new FormData;n.append("file",t);const o=new a.Lr({Accept:"application/json"});return this.http.post(this.apiUrl,n,{headers:o})}static#e=this.\u0275fac=function(n){return new(n||i)(e.KVO(a.Qq))};static#t=this.\u0275prov=e.jDH({token:i,factory:i.\u0275fac,providedIn:"root"})}return i})(),d=(()=>{class i{constructor(t){this.sincronizarService=t,this.selectedFile=null}onFileSelected(t){this.selectedFile=t.target.files[0]}onUpload(){this.selectedFile?this.sincronizarService.uploadExcel(this.selectedFile).subscribe(t=>{console.log("Archivo subido con \xe9xito",t)},t=>{console.error("Error al subir el archivo:",t)}):console.log("Por favor selecciona un archivo.")}static#e=this.\u0275fac=function(n){return new(n||i)(e.rXU(l))};static#t=this.\u0275cmp=e.VBU({type:i,selectors:[["app-sincronizar"]],standalone:!0,features:[e.aNF],decls:26,vars:0,consts:[[1,"auth-main"],[1,"auth-wrapper","v3"],[1,"auth-form"],[1,"card","my-5"],[1,"card-body"],[1,"mb-4"],[1,"mb-5"],[1,"form-group","mb-3"],["for","excelFile",1,"form-label"],[1,"input-group"],["type","file","id","excelFile",1,"form-control",3,"change"],[1,"btn","btn-primary",3,"click"],[1,"d-grid"],[1,"btn","btn-primary"]],template:function(n,o){1&n&&(e.j41(0,"div",0)(1,"div",1)(2,"div",2)(3,"div",3)(4,"div",4)(5,"h3",5)(6,"b"),e.EFF(7,"Sincronizaci\xf3n"),e.k0s()(),e.j41(8,"div",6)(9,"h5")(10,"b"),e.EFF(11,"Importar archivo de Excel a MySQL"),e.k0s()(),e.j41(12,"div",7)(13,"label",8),e.EFF(14,"Campo del archivo"),e.k0s(),e.j41(15,"div",9)(16,"input",10),e.bIt("change",function(u){return o.onFileSelected(u)}),e.k0s(),e.j41(17,"button",11),e.bIt("click",function(){return o.onUpload()}),e.EFF(18,"Subir"),e.k0s()()()(),e.j41(19,"div",5)(20,"h5")(21,"b"),e.EFF(22,"Importar archivo de MySQL a ZOHO"),e.k0s()(),e.j41(23,"div",12)(24,"button",13),e.EFF(25,"Sincronizar"),e.k0s()()()()()()()())},dependencies:[s.MD]})}return i})()}}]);
"use strict";(self.webpackChunkmantis_free_version=self.webpackChunkmantis_free_version||[]).push([[595],{9595:(f,c,o)=>{o.r(c),o.d(c,{default:()=>h});var g=o(7681),s=o(4341),l=o(177),n=o(3953),m=o(678);const d=()=>["/register"];function u(a,_){if(1&a&&(n.j41(0,"div",21)(1,"div",22)(2,"button",23),n.nrm(3,"img",24),n.j41(4,"span",25),n.EFF(5),n.k0s()()()()),2&a){const t=_.$implicit;n.R7$(3),n.Mz_("alt","",t.name," img"),n.Y8G("src",t.image,n.B4B),n.R7$(2),n.JRh(t.name)}}let h=(()=>{class a{constructor(t,r){this.loginService=t,this.router=r,this.email="",this.password="",this.SignInOptions=[{image:"assets/images/authentication/google.svg",name:"Google"},{image:"assets/images/authentication/twitter.svg",name:"Twitter"},{image:"assets/images/authentication/facebook.svg",name:"Facebook"}]}login(){this.loginService.login(this.email,this.password).subscribe({next:t=>{this.router.navigate(["/dashboard/default"])},error:t=>{console.error("Error:",t.message),alert("Error al iniciar sesi\xf3n. Por favor, intenta nuevamente.")}})}static#n=this.\u0275fac=function(r){return new(r||a)(n.rXU(m.H),n.rXU(g.Ix))};static#t=this.\u0275cmp=n.VBU({type:a,selectors:[["app-login"]],standalone:!0,features:[n.aNF],decls:30,vars:5,consts:[[1,"auth-main"],[1,"auth-wrapper","v3"],[1,"auth-form"],[1,"auth-header"],["href","javascript:"],["src","assets/images/logo-dark.svg","alt","logo"],[1,"card","my-5"],[1,"card-body"],[1,"d-flex","justify-content-between","align-items-end","mb-4"],[1,"mb-0"],[1,"link-primary",3,"routerLink"],[1,"form-group","mb-3"],["for","email",1,"form-label"],["type","email","id","email","placeholder","Email Address",1,"form-control",3,"ngModelChange","ngModel"],["for","password",1,"form-label"],["type","password","id","password","placeholder","Password",1,"form-control",3,"ngModelChange","ngModel"],[1,"d-grid","mt-4"],["type","button",1,"btn","btn-primary",3,"click"],[1,"saprator","mt-3"],[1,"row"],["class","col-4",4,"ngFor","ngForOf"],[1,"col-4"],[1,"d-grid"],["type","button",1,"btn","mt-2","btn-light-primary","bg-light","text-muted"],[3,"src","alt"],[1,"d-none","d-sm-inline-block"]],template:function(r,e){1&r&&(n.j41(0,"div",0)(1,"div",1)(2,"div",2)(3,"div",3)(4,"a",4),n.nrm(5,"img",5),n.k0s()(),n.j41(6,"div",6)(7,"div",7)(8,"div",8)(9,"h3",9)(10,"b"),n.EFF(11,"Login"),n.k0s()(),n.j41(12,"a",10),n.EFF(13,"Don't have an account?"),n.k0s()(),n.j41(14,"div",11)(15,"label",12),n.EFF(16,"Email Address"),n.k0s(),n.j41(17,"input",13),n.mxI("ngModelChange",function(i){return n.DH7(e.email,i)||(e.email=i),i}),n.k0s()(),n.j41(18,"div",11)(19,"label",14),n.EFF(20,"Password"),n.k0s(),n.j41(21,"input",15),n.mxI("ngModelChange",function(i){return n.DH7(e.password,i)||(e.password=i),i}),n.k0s()(),n.j41(22,"div",16)(23,"button",17),n.bIt("click",function(){return e.login()}),n.EFF(24,"Login"),n.k0s()(),n.j41(25,"div",18)(26,"span"),n.EFF(27,"Login with"),n.k0s()(),n.j41(28,"div",19),n.DNE(29,u,6,4,"div",20),n.k0s()()()()()()),2&r&&(n.R7$(12),n.Y8G("routerLink",n.lJ4(4,d)),n.R7$(5),n.R50("ngModel",e.email),n.R7$(4),n.R50("ngModel",e.password),n.R7$(8),n.Y8G("ngForOf",e.SignInOptions))},dependencies:[g.iI,g.Wk,s.YN,s.me,s.BC,s.vS,l.MD,l.Sq],styles:['.auth-main[_ngcontent-%COMP%]{position:relative}.auth-main[_ngcontent-%COMP%]   .auth-wrapper[_ngcontent-%COMP%]{height:100%;width:100%;min-height:100vh}.auth-main[_ngcontent-%COMP%]   .auth-wrapper[_ngcontent-%COMP%]   .saprator[_ngcontent-%COMP%]{position:relative;display:flex;align-self:center;justify-content:center}.auth-main[_ngcontent-%COMP%]   .auth-wrapper[_ngcontent-%COMP%]   .saprator[_ngcontent-%COMP%]:after{content:"";position:absolute;top:50%;left:0;width:100%;height:1px;background:var(--bs-border-color);z-index:1}.auth-main[_ngcontent-%COMP%]   .auth-wrapper[_ngcontent-%COMP%]   .saprator[_ngcontent-%COMP%]   span[_ngcontent-%COMP%]{font-size:.875rem;padding:8px 24px;background:#fff;z-index:5;text-transform:capitalize;color:#262626;font-weight:500}.auth-main[_ngcontent-%COMP%]   .auth-wrapper.v3[_ngcontent-%COMP%]{display:flex;align-items:center}.auth-main[_ngcontent-%COMP%]   .auth-wrapper.v3[_ngcontent-%COMP%]   .auth-form[_ngcontent-%COMP%]{flex-direction:column;background:url(img-auth-bg.c28bbbadbe67914d.svg);min-height:100vh;padding:24px;background-repeat:no-repeat;background-size:auto 82%;background-position:left bottom;position:relative;justify-content:space-between}.auth-main[_ngcontent-%COMP%]   .auth-wrapper.v3[_ngcontent-%COMP%]   .auth-form[_ngcontent-%COMP%] > *[_ngcontent-%COMP%]{position:relative;z-index:5}.auth-main[_ngcontent-%COMP%]   .auth-wrapper.v3[_ngcontent-%COMP%]   .auth-form[_ngcontent-%COMP%]:after{content:"";position:absolute;top:0;left:0;width:100%;height:100%;background:#fff3;-webkit-backdrop-filter:blur(16px);backdrop-filter:blur(16px)}.auth-main[_ngcontent-%COMP%]   .auth-wrapper[_ngcontent-%COMP%]   .auth-form[_ngcontent-%COMP%]{display:flex;align-items:center;justify-content:center;flex-grow:1}.auth-main[_ngcontent-%COMP%]   .auth-wrapper[_ngcontent-%COMP%]   .auth-form[_ngcontent-%COMP%]   .card[_ngcontent-%COMP%]{width:100%;max-width:495px;box-shadow:none}.auth-main[_ngcontent-%COMP%]   .auth-wrapper[_ngcontent-%COMP%]   .auth-form[_ngcontent-%COMP%]   img[_ngcontent-%COMP%] + span[_ngcontent-%COMP%]{padding-left:15px}.auth-main[_ngcontent-%COMP%]   .auth-wrapper[_ngcontent-%COMP%]   .auth-form[_ngcontent-%COMP%]   h5[_ngcontent-%COMP%]   span[_ngcontent-%COMP%]{text-decoration:underline}.auth-main[_ngcontent-%COMP%]   .auth-wrapper[_ngcontent-%COMP%]   .auth-footer[_ngcontent-%COMP%], .auth-main[_ngcontent-%COMP%]   .auth-wrapper[_ngcontent-%COMP%]   .auth-header[_ngcontent-%COMP%]{width:100%;display:flex;align-items:center;justify-content:space-between}form[_ngcontent-%COMP%]   i[_ngcontent-%COMP%]{display:inline-flex;align-items:center;justify-content:center;margin:0 -12px 0 0;cursor:pointer;padding:12px;font-size:18px;position:absolute;top:203px;right:45px}']})}return a})()}}]);
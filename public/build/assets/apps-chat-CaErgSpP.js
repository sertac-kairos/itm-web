var l=Object.defineProperty;var r=(t,s,e)=>s in t?l(t,s,{enumerable:!0,configurable:!0,writable:!0,value:e}):t[s]=e;var a=(t,s,e)=>r(t,typeof s!="symbol"?s+"":s,e);import{S as o}from"./index-2T3OL1fW.js";import"./isObjectLike-vZHY8t3n.js";class m{constructor(){a(this,"getMessageHTML",s=>`<li class="chat-group odd" id="odd-1">
                    <img src="/images/users/avatar-1.jpg" class="avatar-sm rounded-circle" alt="avatar-1" />

                    <div class="chat-body">
                        <div>
                            <h6 class="d-inline-flex">You.</h6>
                            <h6 class="d-inline-flex text-muted">10:05pm</h6>
                        </div>

                        <div class="chat-message">
                            <p>${s}</p>

                            <div class="chat-actions dropdown">
                                <button class="btn btn-sm btn-link" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>

                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#"><i class="ti ti-copy fs-14 align-text-top me-1"></i>
                                        Copy Message</a>
                                    <a class="dropdown-item" href="#"><i class="ti ti-edit-circle fs-14 align-text-top me-1"></i>
                                        Edit</a>
                                    <a class="dropdown-item" href="#" data-dismissible="#odd-1"><i class="ti ti-trash fs-14 align-text-top me-1"></i>Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>`);a(this,"addNewMessage",s=>{this.messagesList&&(this.messagesList.innerHTML+=this.getMessageHTML(s),this.scrollToBottom(!0))});a(this,"initForm",()=>{var s;(s=this.chatForm)==null||s.addEventListener("submit",e=>{e.preventDefault();const i=Object.fromEntries(new FormData(e.target).entries());i.message&&(i.message.trim().length===0?this.chatForm.reset():(this.chatInput.value=" ",this.addNewMessage(i.message)))})});a(this,"scrollToBottom",(s=!1)=>{if(this.messagesSimplebar&&this.messagesSimplebar.getScrollElement()){const e=this.messagesSimplebar.getScrollElement().scrollHeight;s&&(this.messagesSimplebar.getScrollElement().style.scrollBehavior="smooth"),this.messagesSimplebar.getScrollElement().scrollTop=e}});a(this,"init",()=>{this.scrollToBottom(),this.initForm()});this.messagesScrollWrapper=document.querySelector('[data-apps-chat="messages-scroll-wrapper"]'),this.messagesList=document.querySelector('[data-apps-chat="messages-list"]'),this.messagesSimplebar=null,this.chatForm=document.querySelector("#chat-form"),this.chatForm&&(this.chatInput=this.chatForm.querySelector("input")),this.messagesScrollWrapper&&(this.messagesSimplebar=new o(this.messagesScrollWrapper))}}new m().init();

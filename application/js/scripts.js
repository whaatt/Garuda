/*! JavaScript Code !*/

/*! AJAX Requests !*/

function message(page,id,type,app,fade,obj,call){call=(typeof call==='undefined')?function(){}:call;dt='html';if(type==1){dt='json';}string='';for(var i=0;i<obj.length;i++){string+='&'+$(obj[i]).serialize();}$.ajax({type:"POST",url:page,data:string,dataType:dt,success:function(data){if(id!=''){if(dt=='html'){if(fade==0){if(app==0){$('#'+id).html(data);}else{$(data).prependTo('#'+id);}}else{if(app==0){$('#'+id).html(data).hide().fadeIn();}else{$(data).hide().prependTo('#'+id).fadeIn();}}}else{if(fade==0){if(app==0){$('#'+id).html(data.html);}else{$('#'+id).prepend(data.html);}}else{if(app==0){$('#'+id).html(data.html).hide().fadeIn();}else{$('#'+id).hide().prepend(data.html).fadeIn();}}}}call();}})}
function po(params,values){ret="<form>";for(var i=0;i<params.length;i++){ret+="<input type='hidden' name='"+params[i]+"' value='"+values[i]+"'>"}ret+="</form>";return ret;}//To be serialized by messsage().

/*! Combined Packages !*/

//Links

function go_register(){message('register.php','content',0,0,0,[po(['submit'],['0'])]); message('menu.php','navigation',0,0,0,[po(['link'],['Register'])]);}
function go_login(){message('login.php','content',0,0,0,[po(['submit'],['0'])]); message('menu.php','navigation',0,0,0,[po(['link'],['Login'])]);}
function go_welcome(){message('welcome.php','content',0,0,0,[]); message('menu.php','navigation',0,0,0,[po(['link'],['Welcome'])]);}

function go_dashboard(){message('dashboard.php','content',0,0,0,[]); message('menu.php','navigation',0,0,0,[po(['link'],['Dashboard'])]);}
function go_create(){message('create.php','content',0,0,0,[po(['submit'],['0'])]); message('menu.php','navigation',0,0,0,[po(['link'],['Create'])]);}
function go_join(){message('join.php','content',0,0,0,[po(['submit'],['0'])]); message('menu.php','navigation',0,0,0,[po(['link'],['Join'])]);}
function go_account(){message('account.php','content',0,0,0,[po(['submit'],['0'])]); message('menu.php','navigation',0,0,0,[po(['link'],['Account'])]);}
function go_logout(){message('logout.php','content',0,1,1,[po(['really'],['0'])]);}

function go_tournament(){message('tournament.php','content',0,0,0,[po(['submit'],['0'])]); message('menu.php','navigation',0,0,0,[po(['link'],['Tournament'])]);}
function go_members(){message('members.php','content',0,0,0,[po(['submit'],['0'])]); message('menu.php','navigation',0,0,0,[po(['link'],['Members'])]);}
function go_tossups(){message('tossups.php','content',0,0,0,[po(['submit'],['0'])]); message('menu.php','navigation',0,0,0,[po(['link'],['Tossups'])]);}
function go_bonuses(){message('bonuses.php','content',0,0,0,[po(['submit'],['0'])]); message('menu.php','navigation',0,0,0,[po(['link'],['Bonuses'])]);}
function go_packets(){message('packets.php','content',0,0,0,[po(['submit'],['0'])]); message('menu.php','navigation',0,0,0,[po(['link'],['Packets'])]);}
function go_leave(){message('leave.php','content',0,1,1,[po(['really'],['0'])]);}

//Continuations

function cont_dashboard(){go_dashboard(); message('greeting.php','greeting',0,0,0,[]);}
function cont_logout(){message('logout.php','',0,0,0,[po(['really'],['1'])]); message('greeting.php','greeting',0,0,0,[]); go_welcome();}
function cont_remove(obj,fade){if(fade==1){$(obj).fadeOut();}else{$(obj).hide();}}
function cont_tournament(id){message('tournament.php','content',0,0,0,[po(['tou_id','submit'],[id,'0'])]); message('menu.php','navigation',0,0,0,[po(['link'],['Tournament'])]);}
function cont_leave(){message('leave.php','',0,0,0,[po(['really'],['1'])]); go_dashboard();}
function cont_delete(type, id){message('delete.php','',0,0,0,[po(['type', 'id'],[type, id])]); if (type=='set'){cont_leave();} else if (type=='member'){go_members();}}

//Submissions

function submit_register(obj){message('register.php','registerform',0,0,1,[po(['submit'],['1']),obj]);}
function submit_login(obj){message('login.php','loginform',0,0,1,[po(['submit'],['1']),obj]);}
function submit_create(obj){message('create.php','createform',0,0,1,[po(['submit'],['1']),obj]);}
function submit_join(obj){message('join.php','joinform',0,0,1,[po(['submit'],['1']),obj]);}
function submit_account(obj){message('account.php','accountform',0,0,1,[po(['submit'],['1']),obj]);}
function submit_update(obj){message('tournament.php','content',0,1,1,[po(['submit'],['1']),obj]); $(document).trigger('close.facebox');}
function submit_member(obj){message('members.php','content',0,1,1,[po(['submit'],['1']),obj]); $(document).trigger('close.facebox');}
function submit_delete_set(){message('tournament.php','content',0,1,1,[po(['submit'],['2'])]); $(document).trigger('close.facebox');}
function submit_delete_member(id){message('members.php','content',0,1,1,[po(['submit','id'],['2',id])]); $(document).trigger('close.facebox');}

//Initializers

function init_menu(){message('menu.php','navigation',0,0,0,[]);}
function init_content(){message('content.php','content',0,0,0,[]);}
function init_greeting(){message('greeting.php','greeting',0,0,0,[]);}
function init_all(){init_menu(); init_content(); init_greeting();}

//Fancifiers

function fancy_sets(id){$('#'+id).dataTable({"bJQueryUI":true,"sPaginationType":"full_numbers","oLanguage":{"sZeroRecords":"No tournaments found! You might want to <a onclick=\"go_create(); return false;\"><span>create</span></a> or <a onclick=\"go_join(); return false;\">join</a> one."}});}
function fancy_members(id){$('#'+id).dataTable({"bJQueryUI":true,"sPaginationType":"full_numbers","oLanguage":{"sZeroRecords":"No members found! That sounds a little sketchy."}});}
function fancy_date(id){$('#'+id).datetimepicker({dateFormat: 'yy-mm-dd', timeFormat: 'hh:mm:ss'});}

//Modals

function modal_set(){message('modal.php','modal',0,0,0,[po(['type'],['set'])],function(){jQuery.facebox({div: '#modal', opacity: 0.6}); $('#modal').html(''); fancy_date('upd_date');});}
function modal_member(id){message('modal.php','modal',0,0,0,[po(['type','id'],['member',id])],function(){jQuery.facebox({div: '#modal', opacity: 0.6}); $('#modal').html('');});}

/*! End JavaScript Code !*/
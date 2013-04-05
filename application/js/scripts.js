/*! JavaScript Code !*/

/*! AJAX Requests !*/

function message(page,id,type,app,fade,obj,call){call=(typeof call==='undefined')?function(){}:call;dt='html';if(type==1){dt='json';}string='';for(var i=0;i<obj.length;i++){string+='&'+$(obj[i]).serialize();}$.ajax({type:"POST",url:page,data:string,dataType:dt,success:function(data){if(id!=''){if(dt=='html'){if(fade==0){if(app==0){$('#'+id).html(data);}else{$(data).prependTo('#'+id);}}else{if(app==0){$('#'+id).html(data).hide().fadeIn();}else{$(data).hide().prependTo('#'+id).fadeIn();}}}else{if(fade==0){if(app==0){$('#'+id).html(data.html);}else{$('#'+id).prepend(data.html);}}else{if(app==0){$('#'+id).html(data.html).hide().fadeIn();}else{$('#'+id).hide().prepend(data.html).fadeIn();}}}}call();}})}
function po(params,values){ret="<form>";for(var i=0;i<params.length;i++){ret+="<input type='hidden' name='"+params[i]+"' value='"+values[i]+"'>"}ret+="</form>";return ret;}//To be serialized by messsage().

/*! Combined Packages !*/

//Links

function go_register(){message('register.php','content',0,0,1,[po(['submit'],['0'])], function(){message('menu.php','navigation',0,0,0,[po(['link'],['Register'])]);});}
function go_login(){message('login.php','content',0,0,1,[po(['submit'],['0'])], function(){message('menu.php','navigation',0,0,0,[po(['link'],['Login'])]);});}
function go_welcome(fade){fade = typeof fade !== 'undefined' ? fade : 0; message('welcome.php','content',0,0,1,[], function(){message('menu.php','navigation',0,0,fade,[po(['link'],['Welcome'])]);});}

function go_dashboard(fade){fade = typeof fade !== 'undefined' ? fade : 0; message('dashboard.php','content',0,0,1,[], function(){message('menu.php','navigation',0,0,fade,[po(['link'],['Dashboard'])]);});}
function go_create(){message('create.php','content',0,0,1,[po(['submit'],['0'])], function(){message('menu.php','navigation',0,0,0,[po(['link'],['Create'])]);});}
function go_join(){message('join.php','content',0,0,1,[po(['submit'],['0'])], function(){message('menu.php','navigation',0,0,0,[po(['link'],['Join'])]);});}
function go_account(){message('account.php','content',0,0,1,[po(['submit'],['0'])], function(){message('menu.php','navigation',0,0,0,[po(['link'],['Account'])]);});}
function go_logout(){message('logout.php','content',0,1,1,[po(['really'],['0'])]);}

function go_tournament(){message('tournament.php','content',0,0,1,[po(['submit'],['0'])], function(){message('menu.php','navigation',0,0,0,[po(['link'],['Tournament'])]);});}
function go_members(){message('members.php','content',0,0,1,[po(['submit'],['0'])], function(){message('menu.php','navigation',0,0,0,[po(['link'],['Members'])]);});}
function go_stats(){message('stats.php','content',0,0,1,[po(['submit'],['0'])], function(){message('menu.php','navigation',0,0,0,[po(['link'],['Stats'])]);});}
function go_import(){message('import.php','content',0,0,1,[po(['submit'],['0'])], function(){message('menu.php','navigation',0,0,0,[po(['link'],['Import'])]);});}
function go_tossups(){message('tossups.php','content',0,0,1,[po(['submit'],['0'])], function(){message('menu.php','navigation',0,0,0,[po(['link'],['Tossups'])]);});}
function go_bonuses(){message('bonuses.php','content',0,0,1,[po(['submit'],['0'])], function(){message('menu.php','navigation',0,0,0,[po(['link'],['Bonuses'])]);});}
function go_packets(){message('packets.php','content',0,0,1,[po(['submit'],['0'])], function(){message('menu.php','navigation',0,0,0,[po(['link'],['Packets'])]);});}
function go_leave(){message('leave.php','content',0,1,1,[po(['really'],['0'])]);}

//Continuations

function cont_dashboard(){go_dashboard(1); message('greeting.php','greeting',0,0,0,[]);}
function cont_logout(){message('logout.php','',0,0,0,[po(['really'],['1'])], function(){message('greeting.php','greeting',0,0,0,[], function(){go_welcome(1);});});}
function cont_remove(obj,fade){if(fade==1){$(obj).fadeOut();}else{$(obj).hide();}}
function cont_tournament(id){message('tournament.php','content',0,0,1,[po(['tou_id','submit'],[id,'0'])], function(){message('menu.php','navigation',0,0,1,[po(['link'],['Tournament'])]);});}
function cont_leave(){message('leave.php','',0,0,0,[po(['really'],['1'])], function(){go_dashboard(1);});}
function cont_delete(type, id){message('delete.php','',0,0,0,[po(['type', 'id'],[type, id])], function(){if (type=='set'){cont_leave();} else if (type=='member'){go_members();} else if (type=='tossup'){go_tossups();} else if (type=='bonus'){go_bonuses();}})}

//Submissions

function submit_register(obj){message('register.php','registerform',0,0,1,[po(['submit'],['1']),obj]);}
function submit_login(obj){message('login.php','loginform',0,0,1,[po(['submit'],['1']),obj]);}
function submit_create(obj){message('create.php','createform',0,0,1,[po(['submit'],['1']),obj]);}
function submit_join(obj){message('join.php','joinform',0,0,1,[po(['submit'],['1']),obj]);}
function submit_import(obj){message('import.php','importform',0,0,1,[po(['submit'],['1']),obj]);}
function submit_account(obj){message('account.php','accountform',0,0,1,[po(['submit'],['1']),obj]);}
function submit_update(obj){message('tournament.php','content',0,1,1,[po(['submit'],['1']),obj], function(){$(document).trigger('close.facebox');})}
function submit_member(obj){message('members.php','content',0,1,1,[po(['submit'],['1']),obj], function(){$(document).trigger('close.facebox');})}
function submit_create_tossup(obj){message('tossups.php','content',0,1,1,[po(['submit'],['1']),obj], function(){$(document).trigger('close.facebox');})}
function submit_edit_tossup(obj){message('tossups.php','content',0,1,1,[po(['submit'],['3']),obj], function(){$(document).trigger('close.facebox');})}
function submit_mark_tossup(obj){message('tossups.php','content',0,1,1,[po(['submit'],['4']),obj], function(){$(document).trigger('close.facebox');})}
function submit_send_tossup(obj){message('tossups.php','content',0,1,1,[po(['submit'],['5']),obj], function(){$(document).trigger('close.facebox');})}
function submit_create_bonus(obj){message('bonuses.php','content',0,1,1,[po(['submit'],['1']),obj], function(){$(document).trigger('close.facebox');})}
function submit_edit_bonus(obj){message('bonuses.php','content',0,1,1,[po(['submit'],['3']),obj], function(){$(document).trigger('close.facebox');})}
function submit_mark_bonus(obj){message('bonuses.php','content',0,1,1,[po(['submit'],['4']),obj], function(){$(document).trigger('close.facebox');})}
function submit_send_bonus(obj){message('bonuses.php','content',0,1,1,[po(['submit'],['5']),obj], function(){$(document).trigger('close.facebox');})}
function submit_delete_set(){message('tournament.php','content',0,1,1,[po(['submit'],['2'])], function(){$(document).trigger('close.facebox');})}
function submit_delete_member(id){message('members.php','content',0,1,1,[po(['submit','id'],['2',id])], function(){$(document).trigger('close.facebox');})}
function submit_delete_tossup(id){message('tossups.php','content',0,1,1,[po(['submit','id'],['2',id])], function(){$(document).trigger('close.facebox');})}
function submit_delete_bonus(id){message('bonuses.php','content',0,1,1,[po(['submit','id'],['2',id])], function(){$(document).trigger('close.facebox');})}
function submit_delete_message_tossup(id){message('tossups.php','content',0,1,1,[po(['submit','id'],['6',id])], function(){$(document).trigger('close.facebox');})}
function submit_delete_message_bonus(id){message('bonuses.php','content',0,1,1,[po(['submit','id'],['6',id])], function(){$(document).trigger('close.facebox');})}
function submit_packets_assign(obj){message('packets.php','content',0,1,1,[po(['submit'],['1']),obj], function(){$(document).trigger('close.facebox');})}
function submit_packets_auto(obj){message('packets.php','content',0,1,1,[po(['submit'],['3']),obj], function(){$(document).trigger('close.facebox');})}

//Initializers

function init_menu(){message('menu.php','navigation',0,0,0,[]);}
function init_content(){message('content.php','content',0,0,1,[]);}
function init_greeting(){message('greeting.php','greeting',0,0,0,[]);}
function init_all(){init_menu(); init_content(); init_greeting();}

//Fancifiers

function fancy_sets(id){$('#'+id).dataTable({"bJQueryUI":true,"sPaginationType":"full_numbers","oLanguage":{"sZeroRecords":"No tournaments found! You might want to <a onclick=\"go_create(); return false;\"><span>create</span></a> or <a onclick=\"go_join(); return false;\">join</a> one."}});}
function fancy_members(id){$('#'+id).dataTable({"bJQueryUI":true,"sPaginationType":"full_numbers","oLanguage":{"sZeroRecords":"No members found! That sounds a little sketchy."}});}
function fancy_tossups(id){$('#'+id).dataTable({"bJQueryUI":true,"sPaginationType":"full_numbers","oLanguage":{"sZeroRecords":"No tossups found! You may want to create a new tossup."},"aoColumnDefs":[{"sType":"num-html","aTargets":[0]},{"sType":"html","aTargets":[3]}]});}
function fancy_bonuses(id){$('#'+id).dataTable({"bJQueryUI":true,"sPaginationType":"full_numbers","oLanguage":{"sZeroRecords":"No bonuses found! You may want to create a new bonus."},"aoColumnDefs":[{"sType":"num-html","aTargets":[0]},{"sType":"html","aTargets":[3]}]});}
function fancy_questions(id){$('#'+id).dataTable({"bJQueryUI":true,"sPaginationType":"full_numbers","oLanguage":{"sZeroRecords":"No promoted questions found. You may want to do so."},"aoColumnDefs":[{"sType":"num-html","aTargets":[0]}]});}
function fancy_packets(id){$('#'+id).dataTable({"bJQueryUI":true,"sPaginationType":"full_numbers","oLanguage":{"sZeroRecords":"No packets found. You can create one above on this page."},"aoColumnDefs":[{"sType":"num-html","aTargets":[0]}]});}
function fancy_date(id){$('#'+id).datetimepicker({dateFormat: 'yy-mm-dd', timeFormat: 'hh:mm:ss'});}

//Modals

function modal_set(){message('modal.php','modal',0,0,0,[po(['type'],['set'])],function(){jQuery.facebox({div: '#modal', opacity: 0.6}); $('#modal').html(''); fancy_date('upd_date');});}
function modal_member(id){message('modal.php','modal',0,0,0,[po(['type','id'],['member',id])],function(){jQuery.facebox({div: '#modal', opacity: 0.6}); $('#modal').html('');});}
function modal_create_tossup(){message('modal.php','modal',0,0,0,[po(['type'],['create_tossup'])],function(){jQuery.facebox({div: '#modal', opacity: 0.6}); $('#modal').html(''); $('#crt_body').redactor(body); $('#crt_ans').redactor(ans);});}
function modal_edit_tossup(id){message('modal.php','modal',0,0,0,[po(['type','id'],['edit_tossup',id])],function(){jQuery.facebox({div: '#modal', opacity: 0.6}); $('#modal').html(''); $('#edt_body').redactor(body); $('#edt_ans').redactor(ans);});}
function modal_mark_tossup(id){message('modal.php','modal',0,0,0,[po(['type','id'],['mark_tossup',id])],function(){jQuery.facebox({div: '#modal', opacity: 0.6}); $('#modal').html('');});}
function modal_send_tossup(id){message('modal.php','modal',0,0,0,[po(['type','id'],['send_tossup',id])],function(){jQuery.facebox({div: '#modal', opacity: 0.6}); $('#modal').html(''); $('#sdt_msg').redactor(msg);});}
function modal_messages_tossup(id){message('modal.php','modal',0,0,0,[po(['type','id'],['messages_tossup',id])],function(){jQuery.facebox({div: '#modal', opacity: 0.6}); $('#modal').html(''); $('#messages').accordion({collapsible: true, active: false});});}
function modal_create_bonus(){message('modal.php','modal',0,0,0,[po(['type'],['create_bonus'])],function(){jQuery.facebox({div: '#modal', opacity: 0.6}); $('#modal').html(''); $('textarea[id^="crb_body"]').redactor(body); $('textarea[id^="crb_ans"]').redactor(ans); $('#crb_lead').redactor(ans);});}
function modal_edit_bonus(id){message('modal.php','modal',0,0,0,[po(['type','id'],['edit_bonus',id])],function(){jQuery.facebox({div: '#modal', opacity: 0.6}); $('#modal').html(''); $('textarea[id^="edb_body"]').redactor(body); $('textarea[id^="edb_ans"]').redactor(ans); $('#edb_lead').redactor(ans);});}
function modal_mark_bonus(id){message('modal.php','modal',0,0,0,[po(['type','id'],['mark_bonus',id])],function(){jQuery.facebox({div: '#modal', opacity: 0.6}); $('#modal').html('');});}
function modal_send_bonus(id){message('modal.php','modal',0,0,0,[po(['type','id'],['send_bonus',id])],function(){jQuery.facebox({div: '#modal', opacity: 0.6}); $('#modal').html(''); $('#sdb_msg').redactor(msg);});}
function modal_messages_bonus(id){message('modal.php','modal',0,0,0,[po(['type','id'],['messages_bonus',id])],function(){jQuery.facebox({div: '#modal', opacity: 0.6}); $('#modal').html(''); $('#messages').accordion({collapsible: true, active: false});});}
function modal_packets_assign(id, tob){message('modal.php','modal',0,0,0,[po(['type','id','tob'],['packets_assign',id,tob])],function(){jQuery.facebox({div: '#modal', opacity: 0.6}); $('#modal').html('');});}
function modal_packets_auto(){message('modal.php','modal',0,0,0,[po(['type'],['packets_auto'])],function(){jQuery.facebox({div: '#modal', opacity: 0.6}); $('#modal').html('');});}

//Editors

var body = {buttons: ['bold', 'italic'], allowedTags: ['b', 'i']};
var ans = {buttons: ['bold', 'italic', 'underline'], allowedTags: ['b', 'i', 'u']};
var msg = {buttons: ['bold', 'italic', 'underline'], allowedTags: ['b', 'i', 'u']};

/*! End JavaScript Code !*/
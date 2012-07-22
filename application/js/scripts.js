/*! JavaScript Code !*/

/*! AJAX Requests !*/

function message(page,id,type,app,fade,obj){dt='html';if(type==1){dt='json';}string='';for(var i=0;i<obj.length;i++){string+='&'+$(obj[i]).serialize();}$.ajax({type:"POST",url:page,data:string,dataType:dt,success:function(data){if(id!=''){if(dt=='html'){if(fade==0){if(app==0){$('#'+id).html(data);}else{$(data).prependTo('#'+id);}}else{if(app==0){$('#'+id).html(data).hide().fadeIn();}else{$(data).hide().prependTo('#'+id).fadeIn();}}}else{if(fade==0){if(app==0){$('#'+id).html(data.html);}else{$('#'+id).prepend(data.html);}}else{if(app==0){$('#'+id).html(data.html).hide().fadeIn();}else{$('#'+id).hide().prepend(data.html).fadeIn();}}}}}})}
function po(params,values){ret="<form>";for(var i=0;i<params.length;i++){ret+="<input type='hidden' name='"+params[i]+"' value='"+values[i]+"'>"}ret+="</form>";return ret;}//To be serialized by messsage().

/*! Combined Packages !*/

//Links

function go_register(){message('register.php','content',0,0,0,[po(['submit'],['0'])]); message('menu.php','navigation',0,0,0,[po(['link'],['Register'])]);}
function go_login(){message('login.php','content',0,0,0,[po(['submit'],['0'])]); message('menu.php','navigation',0,0,0,[po(['link'],['Login'])]);}
function go_welcome(){message('welcome.php','content',0,0,0,[]); message('menu.php','navigation',0,0,0,[po(['link'],['Welcome'])]);}

function go_dashboard(){message('dashboard.php','content',0,0,0,[]); message('menu.php','navigation',0,0,0,[po(['link'],['Dashboard'])]);}
function go_create(){message('create.php','content',0,0,0,[po(['submit'],['0'])]); message('menu.php','navigation',0,0,0,[po(['link'],['Create'])]);}
function go_join(){message('join.php','content',0,0,0,[]); message('menu.php','navigation',0,0,0,[po(['link'],['Join'])]);}
function go_logout(){message('logout.php','content',0,1,1,[po(['really'],['0'])]);}

//Continuations

function cont_dashboard(){go_dashboard(); message('greeting.php','greeting',0,0,0,[]);}
function cont_logout(){message('logout.php','',0,0,0,[po(['really'],['1'])]); message('greeting.php','greeting',0,0,0,[]); go_welcome();}
function cont_remove(name,fade){if(fade==1){$("#" + name).fadeOut();}else{$("#" + name).hide();}}

//Submissions

function submit_register(obj){message('register.php','registerform',0,0,1,[po(['submit'],['1']),obj]);}
function submit_login(obj){message('login.php','loginform',0,0,1,[po(['submit'],['1']),obj]);}
function submit_create(obj){message('create.php','createform',0,0,1,[po(['submit'],['1']),obj]);}

//Initializers

function init_menu(){message('menu.php','navigation',0,0,0,[]);}
function init_content(){message('content.php','content',0,0,0,[]);}
function init_greeting(){message('greeting.php','greeting',0,0,0,[]);}
function init_all(){init_menu(); init_content(); init_greeting();}

//Fancifiers

function fancy_sets(id){$('#'+id).dataTable({"bJQueryUI":true,"sPaginationType":"full_numbers","oLanguage":{"sZeroRecords":"No tournaments found! You might want to <a onclick=\"go_create(); return false;\"><span>create</span></a> or <a>join</a> one."}});}
function fancy_date(id){$('#'+id).datetimepicker({dateFormat: 'yy-mm-dd', timeFormat: 'hh:mm:ss'})};

/*! End JavaScript Code !*/
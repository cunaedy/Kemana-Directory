// validate by ajax
// @param fid = field id to be validated
// @param ajaxUrl = the url for ajax, ajaxUrl must accept 'query' parameter & return 0 or 1
// @param rid = result id (to display the result)
function validateByAjax (fid, ajaxUrl, rid)
{
	var isYes = '<span class="glyphicon glyphicon-ok text-success"></span>';
	var isNo = '<span class="glyphicon glyphicon-remove text-danger"></span>';
	var res = false;
	val = $(fid).val();
	if (val == '') { $(rid).html (''); return false; }
	$.ajax({url:ajaxUrl+'&query='+val, success:function (result, status, xhr)
	{
		var r=$.parseJSON(result);
		if (r[0]) alert ('Warning!\n'+r[1]);
		if (!r[0] && r[2] == 1)
		{
			$(rid).html(isYes);
			res=true;
		}
		else
		{
			$(rid).html(isNo);
			res=false;
		}
	},
	error:function(result,status,xhr) {
		alert ('Error '+result.status+' '+result.statusText+'. Please try again later!');
		res = false;
	}});
	return res;
}

function setCookie(key, value) {
	var expires = new Date();
	expires.setTime(expires.getTime() + (1 * 24 * 60 * 60 * 1000));
	document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
}

function getCookie(key) {
	var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
	return keyValue ? keyValue[2] : null;
}

// simple ajax converts all <a href="url" class="simpleAjax" data-ajax-success-callback="successFunction" data-ajax-success-arg="successFunctionArg" data-ajax-failed-callback="failedFunction" data-ajax-failed-arg="failedFunctionArg"> to an ajax call
// @param class = must be simpleAjax
// @param data-ajax-success-callback = name of function to be called upon success
// @param data-ajax-success-arg = value of argument to be passed (only a single argument)
// @param data-ajax-failed-callback = name of function to be called upon failed, optional
// @param data-ajax-failed-arg = value of argument to be passed (only a single argument), optional
//
// Back end must return in this JSON format: ['error_code', 'error_message', 'res']
// error_code = can be 0 if no error (success), if error_code not 0 (failed), a popup message will be displayed to user.
// error_message = optional
// res = must be 1 for OK, else for NOT OK
//
// Notice! data-ajax-failed-* is different than error_code (or error_message). Error_code should be used for internal error (eg, can not connect to db, etc); while data-ajax-failed-* will be run if res NOT OK.
// eg: <a href="verifyUserName.php?uid=admin" class="simpleAjax" data-ajax-success-callback="okFunc" data-ajax-success-arg="admin" data-ajax-failed-callback="notOkFunc" data-ajax-failed-arg="admin">
// if mySQL error, the backend script should return: ['9999', 'MySql error', '']; // 9999 can be any number, other than 0
// if username has been used: ['0', '', '0']; // first '0' means connection OK, last '0' means error (false), and will execute data-ajax-failed-callback func
// if username has not been used: ['0', '', '1']; // first '0' means connection OK, and will execute data-ajax-success-callback func
$('a.simpleAjax').click(function (event){
 event.preventDefault();
 var that=$(this);
 $.ajax({url: $(this).attr('href'),success:function(result,status,xhr){
	var res = $.parseJSON (result);
	var sCallback = $(that).attr('data-ajax-success-callback') == undefined ? false : $(that).attr('data-ajax-success-callback');
	var sArg = $(that).attr('data-ajax-success-arg') == undefined ? 0 : $(that).attr('data-ajax-success-arg');
	var fCallback = $(that).attr('data-ajax-failed-callback') == undefined ? false : $(that).attr('data-ajax-failed-callback');
	var fArg = $(that).attr('data-ajax-failed-arg') == undefined ? 0 : $(that).attr('data-ajax-failed-arg');
	if (res[0]) alert ('Warning!\n'+res[1]);
	if (!res[0] && res[2] == 1) { if (sCallback) window[sCallback](sArg); } else { if (fCallback) window[fCallback](fArg); }
},
error:function(result,status,xhr) {
	alert ('Error '+result.status+' '+result.statusText+'. Please try again later!');
	res = false;
}});
 return false; });
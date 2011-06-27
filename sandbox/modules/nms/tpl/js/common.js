function completeInsertHost(ret_obj) {
    var message = ret_obj['message'];
    var error = ret_obj['error'];
    var module_srl = ret_obj['module_srl'];
    var page = ret_obj['page'];

    var url = current_url.setQuery('act',ret_obj['act']);
    if(module_srl) url = url.setQuery('module_srl',module_srl);
    if(page) url = url.setQuery('page',page);

    alert(message);

    location.href=url;
}

function completeDeleteHost(ret_obj) {
    var message = ret_obj['message'];
    var error = ret_obj['error'];
    var page = ret_obj['page'];

    var url = current_url.setQuery('act',ret_obj['act']).setQuery('module_srl','');
    if(page) url = url.setQuery('page',page);

    alert(message);

    location.href=url;
}

function completeInsertService(ret_obj) {
    var message = ret_obj['message'];
    var error = ret_obj['error'];
    var module_srl = ret_obj['module_srl'];
    var page = ret_obj['page'];

    var url = current_url.setQuery('act',ret_obj['act']);
    if(module_srl) url = url.setQuery('module_srl',module_srl);
    if(page) url = url.setQuery('page',page);

    alert(message);

    location.href=url;
}

function completeInsertMib(ret_obj) {
    var message = ret_obj['message'];
    var error = ret_obj['error'];
    var module_srl = ret_obj['module_srl'];
    var mib_srl = ret_obj['mib_srl'];
    var page_mib = ret_obj['page_mib'];

    var url = current_url.setQuery('act',ret_obj['act']);
    if(module_srl) url = url.setQuery('module_srl',module_srl);
    if(mib_srl) url = url.setQuery('mib_srl',mib_srl);
    if(page_mib) url = url.setQuery('page_mib',page_mib);

    alert(message);

    location.href=url;
}

function completeDeleteMib(ret_obj) {
    var message = ret_obj['message'];
    var error = ret_obj['error'];
    var page_mib = ret_obj['page_mib'];

    var url = current_url.setQuery('act',ret_obj['act']).setQuery('mib_srl','');
    if(page_mib) url = url.setQuery('page_mib',page_mib);

    alert(message);

    location.href=url;
}

function completeInsertGroup(ret_obj) {
    var message = ret_obj['message'];
    var error = ret_obj['error'];
    var group_srl = ret_obj['group_srl'];
    var page = ret_obj['page'];

    var url = current_url.setQuery('act',ret_obj['act']);
    if(group_srl) url = url.setQuery('group_srl',group_srl);
    if(page) url = url.setQuery('page',page);

    alert(message);

    location.href=url;
}

function completeDeleteGroup(ret_obj) {
    var message = ret_obj['message'];
    var error = ret_obj['error'];
    var page = ret_obj['page'];

    var url = current_url.setQuery('act',ret_obj['act']).setQuery('group_srl','');
    if(page) url = url.setQuery('page',page);

    alert(message);

    location.href=url;
}

function completeInsertSeverity(ret_obj) {
    var message = ret_obj['message'];
    var error = ret_obj['error'];
    var severity = ret_obj['severity'];

    var url = current_url.setQuery('act',ret_obj['act']);
    if(severity) url = url.setQuery('severity',severity);

    alert(message);

    location.href=url;
}

function completeInsertRestore(ret_obj) {
    var message = ret_obj['message'];
    var error = ret_obj['error'];

    var url = current_url.setQuery('act',ret_obj['act']);

    alert(message);

    location.href=url;
}

function wizardStep(ret_obj) {
    var act = ret_obj['act'];
    var step = ret_obj['step'];

    if(!step) step = '';

    var url = current_url.setQuery('act',ret_obj['act']).setQuery('step',step);

    location.href=url;
}

function completeWizard(ret_obj) {
    var message = ret_obj['message'];
    var step = ret_obj['step'];

    if(step==5) {
        var act = ret_obj['act'];
        alert(message);
        var url = current_url.setQuery('act',act).setQuery('step','');
    } else {
        if(message==4) step = message;
        var url = current_url.setQuery('step',step);
    }

    location.href=url;
}

function completeWizardDesc(ret_obj) {
    var message = ret_obj['message'];
    var wizard = ret_obj['wizard'];
    var stat = new Array();
    var row = '';

    stat[0] = wizard.file;
    stat[1] = wizard.title;
    stat[2] = wizard.date;
    stat[3] = wizard.author;
    stat[4] = wizard.count;
    stat[5] = wizard.description;

    (function($){
        $('table.rowTable tr.row').remove();

        for(var i=0;i<stat.length;i++) {
            row = stat[i].split(' : ');
            $('table.rowTable tr:last').before('<tr scope="row" class="row"><th><div>'+row[0]+'</div></th><td><div>'+row[1]+'</div></td></tr>');
        }
    })(jQuery);
}

function completeDeleteSeverity(ret_obj) {
    var message = ret_obj['message'];
    var error = ret_obj['error'];

    var url = current_url.setQuery('act',ret_obj['act']).setQuery('mib_srl','').setQuery('severity','');

    alert(message);

    location.href=url;
}

function completeCheckSMTPInfo(ret_obj) {
    var message = ret_obj['message'];

    alert(message);
}

function complete(ret_obj) {
    var message = ret_obj['message'];

    alert(message);

    location.reload();
}
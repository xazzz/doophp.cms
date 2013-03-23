(function () {
    var URL;
    var tmp = location.protocol.indexOf("file")==-1 ? location.pathname : location.href;
    URL = swfurl + 'static/ueditor/';
	var hash = $('#hash').val();

    window.UEDITOR_CONFIG = {
        UEDITOR_HOME_URL : URL
        ,imageUrl:phpurl+"admin/file.htm?action=ueditor&hash="+hash
        ,imagePath:"" 
        ,toolbars:[["undo","redo","bold","italic","underline","strikethrough","forecolor","backcolor","justifyleft","justifycenter","justifyright","justifyjustify","removeformat","formatmatch","fontsize","pagebreak","insertimage","insertvideo","map","horizontal","spechars","highlightcode","insertunorderedlist","insertorderedlist","unlink","link","selectall","cleardoc","preview","source","fullscreen"]]
        ,labelMap:{
            'anchor':'', 'undo':''
        }
        ,webAppKey:""
        ,initialContent:''
        ,zIndex : 90
        ,pageBreakTag:'_doo_page_'
    };
})();

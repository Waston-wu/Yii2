// 公共函数，需要依赖Jquery
/**
 * 邮箱输入联想功能
 * @param input 输入框对象
 * @param list  列表对象
 */
function mail_input_list(input,list){
    var mailBox = [
        "@qq.com",
        "@sina.com",
        "@163.com",
        "@126.com",
        "@yahoo.com.cn",
        "@gmail.com",
        "@sohu.com"
    ];
    input.bind('keyup', function() {
        var key = input.val();
        if(key.indexOf("@") != -1){
            key = key.slice(0,key.indexOf("@"));
        }
        var mailBoxLen = mailBox.length;
        var html = "";
        for(var i=0; i<mailBoxLen; i++){
            html += '<option value="'+ key + mailBox[i] +'"></option>';
        }
        list.html(html);
    });
}

$(document).ready(function(){
    //代替反斜線(避免HTML驗證器的警告)
    var slash = '/';
    //抓起需動態產生的選單
    var dynamicSelector = $('select[class="dynamicSelector"]');
    //針對每個動態選單預始化與事件設定
    $.each(dynamicSelector,function(){        
        //目前執行的選單
        var selector = $(this);
        //取得設定檔(定義於title屬性中)
        var config = eval('('+selector.attr('title')+')');
        //如果有父層選單，取得父層的預設值
        if(config.parent != null){
            //父選單物件
            var parentSeletor = $(config.parent);
            //父選單目前的值
            var parentValue = ($('option:selected',parentSeletor).attr('value'));
            //初始Ajax的URL網址，並加上變數
            iniUrl = config.url+'&'+config.varname+'='+parentValue;
            //進行遠端讀取選項
            $.getJSON(iniUrl,function(json){
                //該選單的預設值
                var defaultValue = ($('option:selected',selector).attr('value'));
                //用來存放動態產生的選項
                var opts = '';
                $.each(json,function(value,option){
                    if(value == defaultValue){
                        opts += '<option value="'+value+'" selected="selected">'+option+'<'+slash+'option>';
                    } else {
                        opts += '<option value="'+value+'">'+option+'<'+slash+'option>';
                    }                    
                });
                //註冊事件，當父層異動時，重新抓取父層值後更新自身選項
                parentSeletor.change(function(){
                    parentValue = $(this).val();
                    changeUrl = config.url+'&'+config.varname+'='+parentValue;
                    $.getJSON(changeUrl,function(json){
                        defaultValue = ($('option:selected',selector).attr('value'));
                        opts = '';
                        $.each(json,function(value,option){
                            if(value == defaultValue){
                                opts += '<option value="'+value+'" selected="selected">'+option+'<'+slash+'option>';
                            } else {
                                opts += '<option value="'+value+'">'+option+'<'+slash+'option>';
                            }                    
                        });
                        selector.html(opts);
                        $(selector).trigger('change');
                    });
                });
                //刷新選單內的選項
                selector.html(opts);
            });
        } else {
            $.getJSON(config.url,function(json){
                //取得選單中預設的選項
                var defaultValue = ($('option:selected',selector).attr('value'));               
                var opts = '';
                $.each(json,function(value,option){
                    if(value == defaultValue){
                        opts += '<option value="'+value+'" selected="selected">'+option+'</option>';
                    } else {
                        opts += '<option value="'+value+'">'+option+'</option>';
                    }                    
                });
                selector.html(opts);
            });
        }        
    });
});
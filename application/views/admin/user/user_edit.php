<?php $this->load->view('admin/layout/_meta')?>
<title>添加用户</title>
</head>
<body>
<div class="pd-20">
  <div class="Huiform">
    <form action="<?php echo site_url('admin/UserManage/editAction')?>" method="post" id="form-admin-edit">
        <input type="hidden" value="<?php echo $user['id'];?>" name="user_id">
      <table class="table table-bg">
        <tbody>
          <tr>
            <th width="100" class="text-r"><span class="c-red">*</span> 用户名：</th>
            <td><input type="text" style="width:200px" class="input-text" value="<?php echo $user['username'];?>" placeholder="" id="username" name="username" datatype="*2-16" nullmsg="用户名不能为空"></td>
          </tr>
          <tr>
            <th class="text-r"><span class="c-red">*</span> 密码：</th>
            <td><input type="password" style="width:300px" class="input-text" value="" placeholder="" id="user-tel" name="password"></td>
          </tr>
          <tr>
              <th class="text-r"><span class="c-red">*</span> 所属角色：</th>
              <td>
                  <select name="role_id" id="" style="width:200px" class="input-text">
                      <?php foreach ($roleList as $role):?>
                      <option value="<?php echo $role['id']; ?>" <?php if ($role['id']==$user['role_id']) echo "selected"?>><?php echo $role['role_name'];?></option>
                      <?php endforeach;?>
                  </select>
              </td>
          </tr>
          <tr>
            <th></th>
            <td><button class="btn btn-success radius" type="submit" id="submit"><i class="icon-ok"></i> 确定</button></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<!--_footer 作为公共模版分离出去-->
<?php $this->load->view("admin/layout/_footer")?>
<!--/_footer 作为公共模版分离出去-->
<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="/public/admin/lib/jquery.validation/1.14.0/jquery.validate.js"></script>
<script type="text/javascript" src="/public/admin/lib/jquery.validation/1.14.0/validate-methods.js"></script>
<script type="text/javascript" src="/public/admin/lib/jquery.validation/1.14.0/messages_zh.js"></script>
<script>
    $(function () {
        function close() {
            var index = parent.layer.getFrameIndex(window.name);
            parent.layer.close(index);
        }
        $("#form-admin-edit").validate({
            rules:{
                username:{
                    required:true,
                },
                role_id:{
                    required:true,
                },
            },
            onkeyup:false,
            focusCleanup:true,
            success:"valid",
            submitHandler:function(form){
                $(form).ajaxSubmit({
                    success: function (res) {
                        res = JSON.parse(res);
                        if(res.errcode==0){
                            layer.msg('编辑成功!', {icon: 1,time:1000});
                            setTimeout(close,1500);
                        }else{
                            layer.msg('编辑失败!', {icon: 5,time:1500});
                        }
                    }
                });

            }
        });
    })
    /*function edit() {
        var data = $("#edit_form").serialize();
        var url = $("#edit_form").attr('action');
        if($("#username").val()==''){
            layer.msg("请输入用户名！",{icon:2,time:1000});
        }
        if($("#role_id").val()==''){
            layer.msg("请选择角色！",{icon:2,time:1000});
        }
        $.ajax({
            type: 'POST',
            url: url,
            data:data,
            dataType:"JSON",
            success: function(data){
                if(data.errcode==0){
                    layer.msg('编辑成功!',{icon:1,time:1000});
                    setTimeout(close,1000);
                }else{
                    layer.msg('编辑失败!',{icon:5,time:1000});
                }

            },
        });
    }*/
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?080836300300be57b7f34f4b3e97d911";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F080836300300be57b7f34f4b3e97d911' type='text/javascript'%3E%3C/script%3E"));
</script>
</body>
</html>
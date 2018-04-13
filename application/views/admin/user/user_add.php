<?php $this->load->view('admin/layout/_meta')?>
<title>添加用户</title>
</head>
<body>
<div class="pd-20">
  <div class="Huiform">
    <form action="<?php echo site_url('admin/UserManage/addUser')?>" method="post" id="form-admin-add">
      <table class="table table-bg">
        <tbody>
          <tr>
            <th width="100" class="text-r"><span class="c-red">*</span> 用户名：</th>
            <td><input type="text" style="width:200px" class="input-text" value="" placeholder="" id="user-name" name="username" datatype="*2-16" nullmsg="用户名不能为空"></td>
          </tr>
          <tr>
            <th class="text-r"><span class="c-red">*</span> 密码：</th>
            <td><input type="password" style="width:300px" class="input-text" value="" placeholder="" id="password" name="password"></td>
          </tr>
          <tr>
              <th class="text-r"><span class="c-red">*</span> 所属角色：</th>
              <td>
                  <select name="role_id" id="" style="width:200px" class="input-text">
                      <?php foreach ($roleList as $role):?>
                      <option value="<?php echo $role['id'];?>"><?php echo $role['role_name'] ?></option>
                      <?php endforeach;?>
                  </select>
              </td>
          </tr>
          <tr>
            <th></th>
            <td><button class="btn btn-success radius" type="submit"><i class="icon-ok"></i> 确定</button></td>
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
<script type="text/javascript">
$(function () {
    function close() {
        var index = parent.layer.getFrameIndex(window.name);
        parent.layer.close(index);
    }
    $("#form-admin-add").validate({
        rules:{
            username:{
                required:true,
            },
            password:{
                required:true,
            },
            role_id:{
                required:true,
            }
        },
        onkeyup:false,
        focusCleanup:true,
        success:"valid",
        submitHandler:function(form){
            $(form).ajaxSubmit({
                success: function (res) {
                    res = JSON.parse(res);
                    //console.log(res);
                    if(res.errcode==0){
                        layer.msg('添加成功!', {icon: 1,time:1000});
                        setTimeout(function () {
                            close();
                        },1500);
                    }else{
                        layer.msg('添加失败!', {icon: 5,time:1500});
                    }
                }
            });

        }
    });
});
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
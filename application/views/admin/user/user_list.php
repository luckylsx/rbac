<?php $this->load->view('admin/layout/_meta')?>
<![endif]-->
<title>用户管理</title>
<script>
    var userEdit_url = "<?php echo site_url('admin/UserManage/userEdit')?>";
    var add_url = "<?php echo site_url('admin/UserManage/addUser')?>";
</script>
</head>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 用户中心 <span class="c-gray en">&gt;</span> 用户管理 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="pd-20">
  <div class="text-c">
      <!--日期范围：
    <input type="text" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}'})" id="datemin" class="input-text Wdate" style="width:120px;">
    -
    <input type="text" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'datemin\')}',maxDate:'%y-%M-%d'})" id="datemax" class="input-text Wdate" style="width:120px;">-->
      <form action="<?php echo site_url('admin/UserManage/index');?>" method="get">
    <input type="text" class="input-text" style="width:250px" placeholder="输入会员名称、电话、邮箱" id="" name="name" value="<?php echo $name?$name:'';?>">
      <button type="submit" class="btn btn-success" id="" name=""><i class="icon-search"></i> 搜用户</button>
      </form>
  </div>
  <div class="cl pd-5 bg-1 bk-gray mt-20">
    <span class="l">
        <!--<a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="icon-trash"></i> 批量删除</a>-->
    <a href="javascript:;" onclick="user_add('添加用户',add_url,'','310')" class="btn btn-primary radius"><i class="icon-plus"></i> 添加用户</a>
    </span>
    <span class="r">共有数据：<strong><?php echo $total;?></strong> 条</span>
  </div>
  <table class="table table-border table-bordered table-hover table-bg table-sort">
    <thead>
      <tr class="text-c">
        <!--<th width="25"><input type="checkbox" name="" value=""></th>-->
        <th width="100">用户名</th>
        <th width="80">角色</th>
        <th width="90">角色描述</th>
        <th width="130">创建时间</th>
        <th width="80">操作</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($adminList as $admin):?>
      <tr class="text-c">
        <!--<td><input type="checkbox" value="1" name=""></td>-->
        <td><?php echo $admin['username']?></td>
        <td><?php echo $admin['role_name'];?></td>
        <td><?php echo $admin['description'];?></td>
        <td><?php echo $admin['created_at'];?></td>
        <!--<td class="f-14 user-manage">
            <a title="编辑" href="javascript:;" onclick="user_edit('4','550','','编辑','user-add.html')" class="ml-5" style="text-decoration:none"><i class="icon-edit"></i></a>
            <a title="删除" href="javascript:;" onclick="user_del(this,'1')" class="ml-5" style="text-decoration:none"><i class="icon-trash"></i></a>
        </td>-->
          <td class="f-14">
              <a title="编辑" href="javascript:;" onclick="user_edit('编辑用户',userEdit_url+'?user_id='+<?php echo $admin['id'];?>,'','310')" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
              <a title="删除" href="javascript:;" onclick="user_del(this,<?php echo $admin['id'];?>)" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
          </td>
      </tr>
    <?php endforeach;?>
    </tbody>
  </table>
    <?php echo $page_show;?>
</div>
<!--_footer 作为公共模版分离出去-->
<?php $this->load->view("admin/layout/_footer")?>
<!--/_footer 作为公共模版分离出去-->

<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="/public/admin/lib/My97DatePicker/4.8/WdatePicker.js"></script>
<script type="text/javascript" src="/public/admin/lib/datatables/1.10.0/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/public/admin/lib/laypage/1.2/laypage.js"></script>
<script type="text/javascript">
function user_del(obj,id) {
    layer.confirm('用户删除须谨慎，确认要删除吗？',function(roleId){
        $.ajax({
            type: 'POST',
            url: "<?php echo site_url('admin/UserManage/delUser');?>",
            data:{id:id},
            dataType:"JSON",
            success: function(data){
                if(data.errcode==0){
                    $(obj).parents("tr").remove();
                    layer.msg('删除成功!',{icon:1,time:1000});
                }else{
                    layer.msg('删除失败!',{icon:5,time:1500});
                }

            },
        });
    });
}
/*管理员-编辑*/
function user_edit(title,url,w,h){
//    alert(url);
    layer_show(title,url,w,h);
}
/*管理员-角色-添加*/
function user_add(title,url,w,h){
    layer_show(title,url,w,h);
}
</script>
</body>
</html>

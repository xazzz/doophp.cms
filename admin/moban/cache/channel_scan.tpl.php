<? if(!defined('LUA_ROOT')) exit('Access Denied'); include Lua::display('_head',$this->dir); ?>
<div class="luatop">
	<div class="position">Doo：CMS  > <a href="./channel.htm">频道管理</a> > <a href="./channel.htm?action=scan">导入频道</a></div>
</div>
<div class="clear"></div>

<div class="luabox">
	<ul class="columnlist"><? if(is_array($dir)) { foreach($dir as $v) { ?>		<li class="contlist">
			<div class="box"><? include $v['path'].'/cache/update/channel.php'; $data = $data['1']; ?><div class="img" style="height:35px;text-align:center;"><a href="./channel.htm?action=import&amp;path=<?=$v['name']?>" title='点击安装系统' onclick="return confirm('确认?');"><img src="/<?=$v['name']?>/icon.png"  width="32" height="32" /></a></div>
				<h2 class="title"><a href="./channel.htm?action=import&amp;path=<?=$v['name']?>" title='点击安装系统' onclick="return confirm('确认?');"><?=$data['name']?></a><br /><font color='gray'>(<?=$v['name']?>)</font></h2>
			</div>
		</li>
		<? } } ?></ul>
</div>
<? include Lua::display('_foot',$this->dir); ?>

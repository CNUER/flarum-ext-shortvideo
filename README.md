# 短视频解析

# 支持
快手、抖音

# 准备
加入video标签支持
    
    s9e\text-formatter\src\Plugins\BBCodes\Configurator\repository.xml
    
    <bbcode name="VIDEO">
		<usage>[VIDEO poster={URL;optional} src={URL;useContent} height={NUMBER;optional} width={NUMBER;optional}]{TEXT}[/VIDEO]</usage>
		<template><![CDATA[
			<video controls preload="metadata" poster="{@poster}" src="{@src}" height="{@height}" width="{@width}">{TEXT}</video>
		]]></template>
	</bbcode>
    

    
    flarum\flarum-ext-bbcode\src\Listener
    
    $event->configurator->BBCodes->addFromRepository('VIDEO
    
# 使用
直接贴上APP中复制链接生成的地址
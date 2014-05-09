操作说明
=====

一、yum install git -b current
---

二、添加ssh
---
      ssh-keygen -t rsa -C "ssid@yeah.com"  
      ssh-agent bash 
      ssh-add ~/.ssh/id_rsa
      把公钥加到github
      git config --global user.name 'mgbjjin2005'
      git config --global user.email ssid@yeah.net

三、创建一个新的文件夹，初始化
---
      mkdir air
      cd air
      git clone ssh://git@github.com/mgbjjin2005/air.git

四、修改文件提交
---
       编辑readme.md文件
       git add readme.md
       git commit -m "在185测试环境，并修改readme文件"
       git push origin master


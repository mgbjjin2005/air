一、yum install git -b current
二、添加ssh
      ssh-keygen -t rsa -C "ssid@yeah.com"  
      ssh-agent bash 
      ssh-add ~/.ssh/id_rsa
      把公钥加到github
      git config --global user.name 'mgbjjin2005'
      git config --global user.email ssid@yeah.net

三、创建一个新的文件夹，初始化
      mkdir air
      cd air
      git clone ssh://git@github.com/mgbjjin2005/air.git





提交方法
git add mysql/schema.sql 
git add readme.md 
git add sql/wifi.sql 
git commit -m "修改测试"

git clone ssh://git@github.com/mgbjjin2005/air.git
git commit -m "删除一些不用的文件"
git push origin master


Create a new repository on the command line

touch README.md
git init
git add README.md
git commit -m "first commit"
git remote add origin https://github.com/mgbjjin2005/air.git
git push -u origin master

Push an existing repository from the command line

git remote add origin https://github.com/mgbjjin2005/air.git
git push -u origin master

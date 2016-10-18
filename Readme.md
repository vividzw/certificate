# 系统手册
## 前提

#### Github 
<https://github.com/> 注册账号

#### Github source
fork from <https://github.com/wyhw/certificate>

需要管理员赋予权限。


## 安装

#### 安装 Docker

##### Ubuntu
<https://docs.docker.com/engine/installation/linux/ubuntulinux/>

<https://yeasy.gitbooks.io/docker_practice/content/install/ubuntu.html>
	
##### CentOS
<https://docs.docker.com/engine/installation/linux/centos/>

<https://yeasy.gitbooks.io/docker_practice/content/install/centos.html>
	
##### Mac OS X
<https://docs.docker.com/docker-for-mac/>
	
##### Windows
<https://docs.docker.com/docker-for-windows/>
	
#### Install docker compose
<https://docs.docker.com/compose/install/>

#### 安装 git && sourcetree

##### Linux
	yum install git
或

	apt-get install git
	

##### Mac OS X  && Windows

<https://git-scm.com/>


##### SourceTree
<https://www.sourcetreeapp.com/>
	 


## 开发

### Source

	git clone https://github.com/wyhw/certificate.git
或者 from your fork

	git clone https://github.com/your_github_id/certificate.git

### Docker

#### 安装(一般一次即可)

	cd docker/certweb/
	docker-compose up -d

#### 启动
	docker-compose start
	

#### IDE
推荐 PHPStorm <https://www.jetbrains.com/phpstorm/download/>


#### Web source

##### in docker host
	cd web/
##### in workspace container docker
	cd /var/www/laravel
	
#### 安装第三方组件
	cd docker/certweb/
	docker-compose exec workspace bash
	
	composer update
	
	#此命令会重置数据库，请小心使用
	php artisan migrate:refresh
	
#### 注意
	#关闭机器之前最好停止 container docker，避免数据库文件损坏
	cd docker/certweb/
	docker-compose stop
	
	
## 运行
网站 : <http://127.0.0.1:2080/>

PHPMyAdmin: <http://127.0.0.1:2081/>



## 数据备份



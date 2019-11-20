# 灵集公共库项目


### 新项目创建远程 subtree1 为自己起的名称
```
git remote add subtree1 git@github.com:SantiagoFan/php_join.git
```
### 添加目录映射关系
这里将子项目放在了项目extend/join 这个文件夹下了
```
git subtree add --prefix=extend/join subtree1 master
``` 

### 后期修改子项目后提交正常提交就可以  如果是想提交到公共库项目中
```
git subtree push --prefix=extend/join subtree1 master
```
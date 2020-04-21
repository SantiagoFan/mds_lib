# 灵集公共库项目


### 新项目创建远程 php_join 为自己起的名称
```
git remote add php_join git@github.com:SantiagoFan/php_join.git
```
### 添加目录映射关系
这里将子项目放在了项目extend/join 这个文件夹下了
```
git subtree add --prefix=extend/join php_join master
``` 

### 后期修改子项目后提交正常提交就可以  如果是想提交到公共库项目中
```
git subtree push --prefix=extend/join php_join master
```

### 重新拉去最新的子项目
```
git subtree pull --prefix=extend/join php_join master
```

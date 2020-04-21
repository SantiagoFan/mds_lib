# 灵集公共库项目


### 新项目创建远程 mds_lib 为自己起的名称
```
git remote add mds_lib git@github.com:SantiagoFan/mds_lib.git
```
### 添加目录映射关系
这里将子项目放在了项目extend/join 这个文件夹下了
```
git subtree add --prefix=extend/join mds_lib master
``` 

### 后期修改子项目后提交正常提交就可以  如果是想提交到公共库项目中
```
git subtree push --prefix=extend/mds mds_lib master
```

### 重新拉去最新的子项目
```
git subtree pull --prefix=extend/join mds_lib master
```

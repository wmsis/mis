const checkTags = function (vm) {
  return new Promise((resolve, reject) => {
    vm.ajax({
        url: vm.$request.check.getCheckTags,
        params: {},
        success: function (data) {
            let tags = [];
            vm.$_.map(data,function (tag) {
              let md_name = tag.module_name;
              let tg_name = tag.tag_cn_name;
              tags.push({
                  key: tag.id.toString(),
                  label: md_name+'-'+tg_name
                })
            });
            resolve(tags)
        },
        fail(error){
            reject(error)
            console.log(error)
            vm.showNotification('错误','发生错误了','error')
        }
    });
  })
}

export default {
  checkTags:checkTags
}

( function() {
    var vm = new Vue({
        el: document.querySelector('#gdpr-mascot-app'),
        data: function() {
            return {
                showMenu: !1,
                apiUserPlan:mascot_obj.api_user_plan,
                isUserConnected:mascot_obj.is_user_connected,
            }
        },
        computed: (
            {
                boxClass() {
                    return {
                        'gdpr-mascot-quick-links gdpr-mascot-quick-links-open' : this.showMenu,
                        'gdpr-mascot-quick-links' : !this.showMenu,
                    }
                },
                menuItems() {
                    var mItems = [
                        {
                            icon: 'dashicons-lightbulb',
                            tooltip: 'Support',
                            link: mascot_obj.support_url,
                            key: 'support'
                        },
                        {
                            icon: 'dashicons-info',
                            tooltip: 'FAQ',
                            link: mascot_obj.faq_url,
                            key: 'faq'
                        },
                        {
                            icon: 'dashicons-sos',
                            tooltip: 'Documentation',
                            link: mascot_obj.documentation_url,
                            key: 'documentation'
                        }
                    ];
                    if(this.isUserConnected && this.apiUserPlan === "free") {
                        mItems.push({
                            icon: 'dashicons-star-filled',
                            tooltip: 'Upgrade to Pro »',
                            link: '',
                            key: 'upgrade',
                        });
                    }
                    return mItems;
                }
            }
        ),
        methods:{
            buttonClick: function(){
                this.showMenu = !this.showMenu;
            },
            renderElements:function(createElement) {
                var html = [];
                if(this.showMenu) {
                    this.menuItems.forEach((value, index) => {
                        if(value.key === "upgrade") {
                            html.push(createElement('a', {
                                    key: value.key,
                                    class: this.linkClass(value.key),
                                }, [createElement('span', {
                                    class: 'dashicons '+ value.icon
                                }), createElement('span', {
                                    staticClass: 'gdpr-mascot-quick-link-title',
                                    domProps: {
                                        innerHTML: value.tooltip
                                    }
                            })]));
                        } else {
                            html.push(createElement('a', {
                                    key: value.key,
                                    class: this.linkClass(value.key),
                                    attrs: {
                                        href: value.link,
                                        'data-index': index,
                                        target: '_blank'
                                    }
                                }, [createElement('span', {
                                    class: 'dashicons '+ value.icon
                                }), createElement('span', {
                                    staticClass: 'gdpr-mascot-quick-link-title',
                                    domProps: {
                                        innerHTML: value.tooltip
                                    }
                            })]));
                        }
                    })
                }
                return html;
            },
            linkClass: function(key) {
                return 'gdpr-mascot-quick-links-menu-item gdpr-mascot-quick-links-item-' + key;
            },
            enter:function(t,e) {
                var n = 50 * t.dataset.index;
                setTimeout((function() {
                    t.classList.add('gdpr-mascot-show'),
                    e()
                }), n)
            },
            leave:function(t,e) {
                t.classList.remove('gdpr-mascot-show'),
                setTimeout((function() {
                    e()
                }), 200)
            }
        },
        render(createElement){
          return createElement('div',{
              class: this.boxClass,
          }, [
              createElement('button', {
              class: 'gdpr-mascot-quick-links-label',
              on: {
                  click: this.buttonClick
              }
            },[
                createElement('span', {
                    class:'gdpr-mascot-bg-img gdpr-mascot-quick-links-mascot',
                }),
                 
              ]),
              createElement('transition-group', {
                  staticClass: 'gdpr-mascot-quick-links-menu',
                  attrs:{
                      tag: 'div',
                      name: 'gdpr-staggered-fade'
                  },
                  on: {
                      enter: this.enter,
                      leave: this.leave
                  }
              }, this.renderElements(createElement))
          ]);
        },
    });
})();

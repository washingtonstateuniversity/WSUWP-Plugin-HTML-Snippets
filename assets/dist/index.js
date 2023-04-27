!function(){"use strict";var e=window.wp.blocks,t=window.wp.element;const{useState:n,useEffect:r,useRef:a}=wp.element,{useState:i,useRef:l,useEffect:s}=wp.element,{SelectControl:o,Button:c,Spinner:u}=wp.components;(0,e.registerBlockType)("wsuwp/html-snippet",{title:"HTML Snippet",icon:"embed-generic",category:"advanced",attributes:{snippet_id:{type:"string",default:""},show_preview:{type:"boolean",default:!0}},edit:e=>{const{className:d,attributes:p,setAttributes:m}=e,[w,_]=i(!1),g=l(null),{data:h,isLoading:v}=function(e){let t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{};const[i,l]=n({data:null,isLoading:!1,error:null}),s=a(null);return r((()=>(l({data:null,isLoading:!0,error:null}),s.current?.abort(),"undefined"!=typeof AbortController&&(s.current=new AbortController),t={...t,signal:s.current?.signal},(async()=>{try{console.log("Calling: "+e);const n=await fetch(e,t),r=await n.json();n.ok?l({data:r,isLoading:!1,error:null}):l({data:null,isLoading:!1,error:`${r.code} | ${r.message} ${n.status} (${n.statusText})`})}catch(e){l({data:null,isLoading:!1,error:e.message})}})(),()=>{s.current?.abort()})),[e]),i}("/wp-json/wp/v2/wsu_html_snippet");function f(){_(!1),g&&g.current&&(g.current.style.height=null)}if(s((()=>{let e;if(p.show_preview&&(e=setInterval((()=>{const e=g?.current?.contentWindow.document.body?.querySelector("#wsu-gutenberg-snippet-preview")?.offsetHeight;isNaN(e)||(g.current.style.height=e+"px")}),1e3)),e)return()=>clearInterval(e)}),[p.show_preview]),v&&!h)return(0,t.createElement)("p",null,"loading...");if(!v&&!h)return(0,t.createElement)(t.Fragment,null);const b=[{label:"- Select HTML Snippet -",value:""}].concat(h.map((e=>({label:e.title.rendered,value:e.id})))),E=h.find((e=>e.id.toString()===p.snippet_id)),y=function(e){if(!e)return;const t=new URLSearchParams(location.search);return t.set("post",e.id),location.origin+location.pathname+"?"+t.toString()}(E);return(0,t.createElement)(t.Fragment,null,(0,t.createElement)("div",{className:d},(0,t.createElement)("div",{className:`${d}__header`},(0,t.createElement)("div",{className:`${d}__label`},(0,t.createElement)("span",{className:"dashicon dashicons dashicons-embed-generic"}),"HTML Snippet"),(0,t.createElement)("div",{className:`${d}__controls`},y&&(0,t.createElement)(c,{className:`${d}__control is-tertiary`,icon:"edit",href:y,target:"_blank"},"Edit Snippet"),(0,t.createElement)(c,{className:`${d}__control is-tertiary`,icon:p.show_preview?"hidden":"visibility",onClick:()=>{f(),m({show_preview:!p.show_preview})}},p.show_preview?"Hide":"Show"," Preview"))),(0,t.createElement)("div",{className:""},(0,t.createElement)(o,{className:`${d}__select-control`,value:p.snippet_id,options:b,onChange:e=>{f(),m({snippet_id:e})}})),E&&p.show_preview&&!w?(0,t.createElement)(u,{className:`${d}__spinner`}):"",E&&p.show_preview?(0,t.createElement)("iframe",{ref:g,className:`${d}__preview ${w?"loaded":""}`,src:`${E.link}&preview=true`,onLoad:e=>{_(!0)}}):""))},save:function(){return null}})}();
import { useFetch } from "../../hooks";

const { useState, useRef, useEffect } = wp.element;
const { SelectControl, Button, Spinner } = wp.components;
const { InnerBlocks, useBlockProps, InspectorControls } = wp.blockEditor;

const Edit = (props) => {
  const { className, attributes, setAttributes } = props;
  const [previewLoaded, setPreviewLoaded] = useState(false);
  const previewRef = useRef(null);
  const blockClass = "wp-block-wsuwp-html-snippet";
  const blockProps = useBlockProps({
    className: blockClass,
  });

  const apiPath = "/wp-json/wp/v2/wsu_html_snippet";
  const { data, isLoading } = useFetch(`${WSUWP_DATA.siteUrl}${apiPath}`);

  useEffect(() => {
    let sizeRefresher;

    if (attributes.show_preview) {
      sizeRefresher = setInterval(() => {
        const contentHeight =
          previewRef?.current?.contentWindow.document.body?.querySelector(
            "#wsu-gutenberg-snippet-preview"
          )?.offsetHeight;

        if (!isNaN(contentHeight)) {
          previewRef.current.style.height = contentHeight + "px";
        }
      }, 1000);
    }

    if (sizeRefresher) {
      return () => clearInterval(sizeRefresher);
    }
  }, [attributes.show_preview]);

  function reset() {
    setPreviewLoaded(false);
    if (previewRef && previewRef.current) {
      previewRef.current.style.height = null;
    }
  }

  function getEditLink(post) {
    if (!post) return;

    const params = new URLSearchParams(location.search);
    params.set("post", post.id);

    return location.origin + location.pathname + "?" + params.toString();
  }

  if (isLoading && !data) {
    return <p>loading...</p>;
  }

  if (!isLoading && !data) {
    return <></>;
  }

  const options = [{ label: "- Select HTML Snippet -", value: "" }].concat(
    data.map((s) => {
      return { label: s.title.rendered, value: s.id };
    })
  );

  const selectedOption = data.find(
    (o) => o.id.toString() === attributes.snippet_id
  );

  const editLink = getEditLink(selectedOption);

  return (
    <div {...blockProps}>
      <div className={`${blockClass}__header`}>
        <div className={`${blockClass}__label`}>
          <span className={`dashicon dashicons dashicons-embed-generic`}></span>
          HTML Snippet
        </div>
        <div className={`${blockClass}__controls`}>
          {editLink && (
            <Button
              className={`${blockClass}__control is-tertiary`}
              icon="edit"
              href={editLink}
              target="_blank"
            >
              Edit Snippet
            </Button>
          )}
          <Button
            className={`${blockClass}__control is-tertiary`}
            icon={attributes.show_preview ? "hidden" : "visibility"}
            onClick={() => {
              reset();
              setAttributes({ show_preview: !attributes.show_preview });
            }}
          >
            {attributes.show_preview ? "Hide" : "Show"} Preview
          </Button>
        </div>
      </div>
      <div className="">
        <SelectControl
          className={`${blockClass}__select-control`}
          value={attributes.snippet_id}
          options={options}
          onChange={(id) => {
            reset();
            setAttributes({ snippet_id: id });
          }}
        />
      </div>

      {selectedOption && attributes.show_preview && !previewLoaded ? (
        <Spinner className={`${blockClass}__spinner`} />
      ) : (
        ""
      )}

      {selectedOption && attributes.show_preview ? (
        // <div
        //   className={`${blockClass}__preview`}
        //   dangerouslySetInnerHTML={{
        //     __html: selectedOption.content.rendered,
        //   }}
        // ></div>

        <iframe
          ref={previewRef}
          className={`${blockClass}__preview ${previewLoaded ? "loaded" : ""}`}
          src={`${selectedOption.link}&preview=true`}
          onLoad={(e) => {
            // const el = e.target;
            // console.log(e.target);
            setPreviewLoaded(true);

            // setTimeout(() => {
            //   el.style.height =
            //     el.contentWindow.document.body.querySelector(
            //       "#wsu-gutenberg-snippet-preview"
            //     ).offsetHeight + "px";
            // }, 200);
          }}
        ></iframe>
      ) : (
        ""
      )}
    </div>
  );
};

export default Edit;

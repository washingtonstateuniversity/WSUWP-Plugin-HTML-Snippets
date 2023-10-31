import { registerBlockType } from "@wordpress/blocks";

import Edit from "./edit";

registerBlockType("wsuwp/html-snippet", {
  apiVersion: 2,
  title: "HTML Snippet",
  icon: "embed-generic",
  category: "advanced",
  attributes: {
    snippet_id: {
      type: "string",
      default: "",
    },
    show_preview: {
      type: "boolean",
      default: true,
    },
  },
  edit: Edit,
  save: function () {
    return null;
  },
});

# WPGraphQL Adjacent Posts

The WPGraphQL Adjacent Posts plugin extends the WPGraphQL plugin to add fields for fetching adjacent posts. This makes it easy to implement previous/next post
navigation with the data that's supplied by the WPGraphQL plugin. (There may be other ways of approaching this. I've tried a few.)

## Description

Adds four new fields to the `Post` type within the WPGraphQL schema:

- `previousPost`: Fetches the previous post.
- `nextPost`: Fetches the next post.
- `previousPostInCategory`: Fetches the previous post within the same category.
- `nextPostInCategory`: Fetches the next post within the same category.

## Usage

Example query:

```graphql
query GetPostBySlug($id: ID!) {
  post(id: $id, idType: SLUG) {
    id
    title
    content
    slug
    previousPost {
      id
      slug
    }
    nextPost {
      id
      slug
    }
    previousPostInCategory {
      id
      slug
    }
    nextPostInCategory {
      id
      slug
    }
  }
}
```
## Limitations

The plugin doesn't currently handle hierarchical post types (pages or custom hierarchical types). Support for hierarchical post types may be added in the future.
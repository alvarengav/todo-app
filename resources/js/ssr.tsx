import { renderToString } from 'react-dom/server'
import { createInertiaApp } from '@inertiajs/react'
import createServer from '@inertiajs/react/server'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { RouteName, route } from 'ziggy-js'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createServer(page =>
  createInertiaApp({
    page,
    render: renderToString,
    title: title => `${title} - ${appName}`,
    resolve: name =>
      resolvePageComponent(
        `./Pages/${name}.tsx`,
        import.meta.glob('./Pages/**/*.tsx')
      ),
    setup: ({ App, props }) => {
      global.route<RouteName> = (name, params, absolute) =>
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        route(name, params as any, absolute, {
          // @ts-expect-error error
          ...page.props.ziggy,
          // @ts-expect-error error
          location: new URL(page.props.ziggy.location)
        })

      return <App {...props} />
    }
  })
)

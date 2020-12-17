<?php

namespace App\Http\Middleware;

use Closure;

class TransformIdsMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    // get request params
    $routeParameters = $request->route()->parameters();

    // get request data
    $requestData = $request->all();

    // ids list
    $ids = [
      'id'
    ];

    // decode ids
    foreach ($ids as $id) {
      // decode found ids if found params list
      if (isset($routeParameters[$id])) {
        $request->route()->setParameter($id, decodeId($routeParameters[$id]));
      }

      // decode found ids if found in request data list
      if (isset($requestData[$id])) {
        // if data is an array of ids
        if (is_array($requestData[$id])) {
          $requestData[$id] = decodeIds($requestData[$id]);
          continue;
        }
        // assign ID
        $requestData[$id] =  decodeId($requestData[$id]);
      }
    }

    // replace the request data
    $request->replace($requestData);

    // continue request
    return $next($request);
  }
}

<?php

/**
 * @OA\Schema(
 *   schema="Task",
 *   type="object",
 *   title="Task",
 *   required={"id","title","status"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="title", type="string", example="Buy milk"),
 *   @OA\Property(property="description", type="string", example="Get milk from store"),
 *   @OA\Property(property="status", type="string", enum={"pending","completed"}, example="pending"),
 *   @OA\Property(property="created_at", type="string", format="date-time")
 * )
 */

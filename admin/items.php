<div class="flex items-end justify-between gap-4 flex-wrap">
  <div>
    <h1 class="text-3xl font-extrabold">Pending Items</h1>
    <p class="text-gray-400 mt-1">Approve items to make them public.</p>
  </div>
  <a class="text-gray-400 hover:text-gray-200" href="admin/index.php">
    <i class="fa-solid fa-arrow-left mr-2"></i>Admin
  </a>
</div>

<div class="mt-6 bg-card border border-gray-800 rounded-2xl p-6">
  <div class="text-gray-500">No pending items.</div>
  <!-- Example table structure (static, since PHP loop removed) -->
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="text-gray-400">
        <tr class="border-b border-gray-800">
          <th class="py-2 text-left">Item</th>
          <th class="py-2 text-left">Type</th>
          <th class="py-2 text-left">Reporter</th>
          <th class="py-2 text-left">Location</th>
          <th class="py-2 text-left">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr class="border-b border-gray-800/60 hover:bg-gray-800/40">
          <td class="py-3"><a class="hover:text-primary" href="#">Sample Item</a></td>
          <td class="py-3">TYPE</td>
          <td class="py-3">Reporter Name</td>
          <td class="py-3">Location</td>
          <td class="py-3">
            <a class="px-3 py-2 rounded-xl bg-success hover:bg-emerald-700 font-semibold" href="#">Approve</a>
            <a class="ml-2 px-3 py-2 rounded-xl bg-danger hover:bg-red-700 font-semibold" href="#">Reject</a>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<div class="max-w-4xl mx-auto">
  <nav class="text-sm text-gray-500 mb-4">
    <a class="hover:text-gray-300" href="<?= BASE_URL ?>items/view.php?id=<?= $itemId ?>">Item</a>
    <span class="mx-2">/</span>
    <span class="text-gray-300">Submit Claim</span>
  </nav>

  <h1 class="text-3xl font-extrabold">Submit Claim</h1>
  <p class="text-gray-400 mt-1">Provide strong proof. Admin will approve one claimant.</p>

  <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-card border border-gray-800 rounded-2xl p-7">
        <div class="font-semibold"><?= e($item['title']) ?></div>
        <div class="text-sm text-gray-400 mt-1">
          <i class="fa-solid fa-location-dot mr-1"></i><?= e($item['location']) ?>
          <span class="mx-2">•</span>
          <?= date('M d, Y', strtotime($item['date_lost_found'])) ?>
        </div>
      </div>

      <form method="post" class="space-y-4">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
        <div>
          <label class="text-sm text-gray-300">Claim details *</label>
          <textarea name="claim_details" required rows="8"
                    class="mt-2 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 focus:border-primary"
                    placeholder="Unique marks, serial number, contents, purchase info..."></textarea>
        </div>
        <div>
          <label class="text-sm text-gray-300">Additional proof (optional)</label>
          <textarea name="proof_docs" rows="4"
                    class="mt-2 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 focus:border-primary"
                    placeholder="Receipt number, links, details..."></textarea>
        </div>
        <div class="flex items-center justify-between pt-4 border-t border-gray-800">
          <a class="text-gray-400 hover:text-gray-200" href="<?= BASE_URL ?>items/view.php?id=<?= $itemId ?>"><i class="fa-solid fa-arrow-left mr-2"></i>Back</a>
          <button class="bg-primary hover:bg-blue-700 font-semibold px-6 py-3 rounded-xl">
            <i class="fa-solid fa-paper-plane mr-2"></i>Submit Claim
          </button>
        </div>
      </form>
    </div>

    <div class="bg-card border border-gray-800 rounded-2xl p-6">
      <div class="font-bold">Tips for approval</div>
      <ul class="mt-4 text-sm text-gray-400 space-y-2">
        <li><i class="fa-solid fa-check text-success mr-2"></i>Describe contents accurately</li>
        <li><i class="fa-solid fa-check text-success mr-2"></i>Match date/location details</li>
      </ul>
    </div>
  </div>
</div>


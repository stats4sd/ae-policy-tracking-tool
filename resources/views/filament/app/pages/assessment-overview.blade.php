<x-filament-panels::page>

    <div class="flex mx-10 gap-x-10 justify-between" style="max-width: 1000px">
        <x-progress-header-circles :percent="65" :step-name="'Country Status'" :status="'In Progress'"/>
        <x-progress-header-circles :percent="0" :step-name="'Review'" status="-"/>
        <x-progress-header-circles :percent="0" :step-name="'Stakeholder Engagement'" status="-"/>
        <x-progress-header-circles :percent="0" :step-name="'Ongoing Monitoring'" status="-"/>
    </div>

    <x-filament::tabs label="CFS Policy Recommendations" class="mt-8">
        <x-filament::tabs.item
                :active="$activeTab === 'tab1'"
                wire:click="$set('activeTab', 'tab1')"
        >
            1. Policy Foundations
        </x-filament::tabs.item>
        <x-filament::tabs.item
                :active="$activeTab === 'tab2'"
                wire:click="$set('activeTab', 'tab2')"
        >
            2. Measuring Progress
        </x-filament::tabs.item>
        <x-filament::tabs.item
                :active="$activeTab === 'tab3'"
                wire:click="$set('activeTab', 'tab3')"
        >
            3. Fostering Transitions
        </x-filament::tabs.item>
        <x-filament::tabs.item
                :active="$activeTab === 'tab4'"
                wire:click="$set('activeTab', 'tab4')"
        >
            4. Co-creation + Co-learning
        </x-filament::tabs.item>
        <x-filament::tabs.item
                :active="$activeTab === 'tab5'"
                wire:click="$set('activeTab', 'tab5')"
        >
            5. Empowering People
        </x-filament::tabs.item>
    </x-filament::tabs>

    @if($activeTab === 'tab1')

        <h3 class="text-xl font-bold text-center">1. Lay or strengthen, as appropriate, the policy foundations for agroecological approaches to contribute to sustainable agriculture and food systems that enhance food security and nutrition.</h3>
        <hr/>
        <div>
            <div class="grid grid-cols-12 space-x-8">
                <div class="col-span-12 lg:col-span-3 border-r border-r-gray-500 p-4 place-content-center text-center font-bold text-xl">
                    <p>STATUS</p>
                </div>
                <div class="col-span-12 lg:col-span-7 space-y-3">
                    <p>Sit sint pariatur do quis sit nulla deserunt ut qui ea culpa est sint velit exercitation. Dolore esse ipsum velit exercitation aliqua cupidatat nostrud incididunt proident enim eu dolor deserunt ex nisi. Adipisicing do nostrud dolor veniam.</p>
                    <hr/>
                    <p>Eu minim ex pariatur labore eiusmod do adipisicing ea eiusmod exercitation Lorem. Tempor esse nostrud aute laboris aliqua ex quis sint anim nulla excepteur. Sit excepteur enim minim minim consectetur laboris consequat irure duis cillum velit quis.</p>
                </div>
                <div class="col-span-12 lg:col-span-2 place-content-center">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Edit</button>
                </div>
            </div>
            <div class="grid grid-cols-12 space-x-8 mt-8 border-t border-t-gray-500 pt-8">
                <div class="col-span-12 lg:col-span-3 border-r border-r-gray-500 p-4 place-content-center text-center font-bold text-xl">
                    <p>MEASURES CREATING PERVERSE INCENTIVES</p>
                </div>
                <div class="col-span-12 lg:col-span-7 space-y-3">
                    <p>Sit sint pariatur do quis sit nulla deserunt ut qui ea culpa est sint velit exercitation. Dolore esse ipsum velit exercitation aliqua cupidatat nostrud incididunt proident enim eu dolor deserunt ex nisi. Adipisicing do nostrud dolor veniam.</p>
                    <hr/>
                    <p>Eu minim ex pariatur labore eiusmod do adipisicing ea eiusmod exercitation Lorem. Tempor esse nostrud aute laboris aliqua ex quis sint anim nulla excepteur. Sit excepteur enim minim minim consectetur laboris consequat irure duis cillum velit quis.</p>
                    <hr/>
                    <p>Eu minim ex pariatur labore eiusmod do adipisicing ea eiusmod exercitation Lorem. Tempor esse nostrud aute laboris aliqua ex quis sint anim nulla excepteur. Sit excepteur enim minim minim consectetur laboris consequat irure duis cillum velit quis.</p>
                </div>
                <div class="col-span-12 lg:col-span-2 place-content-center">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Edit</button>
                </div>
            </div>

            <div class="grid grid-cols-12 space-x-8 mt-8 border-t border-t-gray-500 pt-8">
                <div class="col-span-12 lg:col-span-3 border-r border-r-gray-500 p-4 place-content-center text-center font-bold text-xl">
                    <p>MEASURES THAT GO BEYOND POLICY RECOMMENDATION</p>
                </div>
                <div class="col-span-12 lg:col-span-7 space-y-3">
                    <p>Sit sint pariatur do quis sit nulla deserunt ut qui ea culpa est sint velit exercitation. Dolore esse ipsum velit exercitation aliqua cupidatat nostrud incididunt proident enim eu dolor deserunt ex nisi. Adipisicing do nostrud dolor veniam. Eu minim ex pariatur labore eiusmod do adipisicing ea eiusmod exercitation Lorem. Tempor esse nostrud aute laboris aliqua ex quis sint anim nulla excepteur. Sit excepteur enim minim minim consectetur laboris consequat irure duis cillum velit quis.</p>
                </div>
                <div class="col-span-12 lg:col-span-2 place-content-center">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Edit</button>
                </div>
            </div>

            <div class="grid grid-cols-12 space-x-8 mt-8 border-t border-t-gray-500 pt-8">
                <div class="col-span-12 lg:col-span-3 border-r border-r-gray-500 p-4 place-content-center text-center font-bold text-xl">
                    <p>CIVIL SOCIETY PERSPECTIVE</p>
                </div>
                <div class="col-span-12 lg:col-span-7 space-y-3">
                    <p>Sit sint pariatur do quis sit nulla deserunt ut qui ea culpa est sint velit exercitation. Dolore esse ipsum velit exercitation aliqua cupidatat nostrud incididunt proident enim eu dolor deserunt ex nisi. Adipisicing do nostrud dolor veniam.</p>

                </div>
                <div class="col-span-12 lg:col-span-2 place-content-center">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Edit</button>
                </div>
            </div>
        </div>
    @endif

</x-filament-panels::page>

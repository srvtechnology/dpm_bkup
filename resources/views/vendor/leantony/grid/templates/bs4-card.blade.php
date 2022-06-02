<div class="row laravel-grid" id="{{ $grid->getId() }}">
    <div class="col-md-12 col-xs-12 col-sm-12">
        <div class="card">
            <div class="header bg-cyan">
                <div class="pull-left">
                    <h2>{{ $grid->renderTitle() }}</h2>
                    {!! $grid->renderPaginationInfoAtHeader() !!}
                </div>

                {!! $grid->renderPaginationLinksSection() !!}
            </div>
            <div class="body">
                @yield('data')
            </div>
            <div class="footer">
                {!! $grid->renderPaginationInfoAtFooter() !!}
                {!! $grid->renderPaginationLinksSection() !!}
            </div>
        </div>
    </div>
</div>